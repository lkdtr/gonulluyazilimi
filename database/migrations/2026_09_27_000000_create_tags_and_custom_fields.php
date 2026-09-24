<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Free labels a manager puts on contacts (e.g. "Basın", "Sponsor").
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique();
            $table->string('color', 20)->default('blue');
            $table->timestamps();
        });

        Schema::create('contact_tag', function (Blueprint $table) {
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->timestamp('created_at')->nullable();
            $table->primary(['contact_id', 'tag_id']);
        });

        // Fields each association defines for its contacts (e.g. a nickname
        // printed on the card, a workplace address). Groups are registered in
        // code (App\Support\CustomFields::GROUPS and modules).
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->string('label', 100);
            $table->string('group', 50)->default('personal');
            // text, textarea, number, date, select, checkbox, email, url, phone
            $table->string('type', 20)->default('text');
            $table->json('options')->nullable();
            $table->text('help')->nullable();
            $table->boolean('is_required')->default(false);
            // hidden: managers only; visible: the person sees it; editable: the person edits it.
            $table->string('member_access', 20)->default('hidden');
            // person, organization or both
            $table->string('applies_to', 20)->default('person');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(100);
            $table->timestamps();
        });

        Schema::create('custom_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('custom_field_id')->constrained()->cascadeOnDelete();
            $table->text('value');
            $table->timestamps();
            $table->unique(['contact_id', 'custom_field_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_field_values');
        Schema::dropIfExists('custom_fields');
        Schema::dropIfExists('contact_tag');
        Schema::dropIfExists('tags');
    }
};
