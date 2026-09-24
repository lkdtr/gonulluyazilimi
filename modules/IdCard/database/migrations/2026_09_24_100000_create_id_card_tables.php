<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One card design per affiliation type (volunteer, member, board...).
        Schema::create('id_card_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliation_type_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('organization_name');
            $table->boolean('is_active')->default(true);
            // The card is shown only once the person has an approved photo.
            $table->boolean('requires_photo')->default(true);
            $table->string('background_color', 7)->default('#ffffff');
            $table->string('text_color', 7)->default('#1d273b');
            $table->string('accent_color', 7)->default('#206bc4');
            $table->string('number_prefix', 10)->default('');
            $table->unsignedTinyInteger('number_digits')->default(6);
            // Keys of App\Modules\ContactFields shown on the card, in order.
            $table->json('fields')->nullable();
            $table->string('footer_text')->nullable();
            // Organization logo on the private "local" disk; not personal data.
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });

        // A card belongs to one affiliation: it is valid while the affiliation
        // lasts, so a card ends with the term or the membership.
        Schema::create('id_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_card_template_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_affiliation_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('serial');
            $table->string('number', 30)->unique();
            // Secret part of the QR verification address; renewing it voids old QR codes.
            $table->string('verify_token', 64)->unique();
            $table->timestamp('revoked_at')->nullable();
            $table->string('revoked_reason')->nullable();
            $table->timestamps();

            $table->unique(['id_card_template_id', 'serial']);
            $table->unique(['id_card_template_id', 'contact_affiliation_id']);
        });

        $now = now();
        $defaults = [
            'volunteer' => ['name' => 'Gönüllü Kartı', 'accent_color' => '#2fb344', 'number_prefix' => 'G-', 'fields' => ['forwarding_email']],
            'member' => ['name' => 'Üye Kartı', 'accent_color' => '#206bc4', 'number_prefix' => 'U-', 'fields' => ['member_number']],
        ];

        foreach ($defaults as $key => $template) {
            $typeId = DB::table('affiliation_types')->where('key', $key)->value('id');
            if ($typeId === null) {
                continue;
            }

            DB::table('id_card_templates')->insert([
                'affiliation_type_id' => $typeId,
                'name' => $template['name'],
                'organization_name' => (string) config('app.name'),
                'accent_color' => $template['accent_color'],
                'number_prefix' => $template['number_prefix'],
                'fields' => json_encode($template['fields']),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('id_cards');
        Schema::dropIfExists('id_card_templates');
    }
};
