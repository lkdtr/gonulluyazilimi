<?php

namespace Modules\Admin;

use App\Models\ContactPhoto;
use App\Models\User;
use App\Modules\Dashboard;
use App\Modules\Menu;
use App\Modules\ModuleServiceProvider;
use App\Modules\Slots;

/**
 * Admin panel home, contacts and their affiliations, users, roles and
 * permissions, identity checks and process logs.
 * Locked: every module adds its own pages to the panel.
 */
class AdminServiceProvider extends ModuleServiceProvider
{
    protected function name(): string
    {
        return 'admin';
    }

    protected function bootModule(Menu $menu, Slots $slots): void
    {
        $menu->label('admin', 'dashboard', null, 'layout-dashboard');
        $menu->add('admin', 'dashboard', 'Genel Bakış', 'admin.dashboard', [1, 2], 1);

        $this->permissions()->group('contacts', 'Kişi & Kurumlar', 10);
        $this->permissions()->register('contacts.view', 'Kişi ve kurumları görebilsin', 'contacts', 10);
        $this->permissions()->register('contacts.manage', 'Kişi ve kurum ekleyip düzenleyebilsin, sıfat verebilsin', 'contacts', 11);
        $this->permissions()->register('photos.review', 'Profil fotoğraflarını onaylayıp reddedebilsin', 'contacts', 12);
        $this->permissions()->register('data-deletion.manage', 'KVKK veri silme taleplerini onaylayıp reddedebilsin', 'contacts', 13);
        $this->permissions()->group('settings', 'Ayarlar', 90);
        $this->permissions()->register('settings.manage', 'Kurum ayarlarını (ad, logo, iletişim, ana sayfa) düzenleyebilsin', 'settings', 89);
        $this->permissions()->register('fields.manage', 'Etiketleri ve özel alanları tanımlayabilsin', 'settings', 89);
        $this->permissions()->register('agreements.manage', 'Sözleşmeleri düzenleyip yayınlayabilsin, kabulleri görebilsin', 'settings', 89);
        $this->permissions()->register('affiliations.manage', 'Sıfat türlerini düzenleyebilsin', 'settings', 90);
        $this->permissions()->register('roles.manage', 'Rolleri, yetkileri ve rol şablonlarını düzenleyebilsin', 'settings', 91);

        $menu->label('admin', 'contacts', 'Kişi & Kurumlar', 'address-book');
        $menu->add('admin', 'contacts', 'Kişi & Kurumlar', 'admin.contacts', ['contacts.view'], 5);
        $menu->add('admin', 'contacts', 'Kişi / kurum ekle', 'admin.contacts.create', ['contacts.manage'], 6);
        $menu->add('admin', 'contacts', 'Fotoğraf onayı', 'admin.photos', ['photos.review'], 7);
        $menu->add('admin', 'contacts', 'Veri silme talepleri', 'admin.data-deletions', ['data-deletion.manage'], 8);

        $this->dashboard()->stat('Başarısız kuyruk işi', 'alert-triangle', fn () => \Illuminate\Support\Facades\DB::table('failed_jobs')->count(), null, [1], 99, 'Gönderilemeyen e-postalar; queue:failed ile incelenir');
        $this->dashboard()->stat('Veri silme talebi', 'user-x', fn () => \App\Models\DataDeletionRequest::pending()->count(), 'admin.data-deletions', ['data-deletion.manage'], 15, 'Değerlendirme bekleyen');
        $this->dashboard()->stat('Onay bekleyen fotoğraf', 'photo-check', fn () => ContactPhoto::pending()->count(), 'admin.photos', ['photos.review'], 14);

        $menu->label('admin', 'settings', 'Ayarlar', 'settings');
        $menu->add('admin', 'settings', 'Kurum ayarları', 'admin.settings.organization', ['settings.manage'], 89);
        $menu->add('admin', 'settings', 'Özel alanlar', 'admin.custom-fields', ['fields.manage'], 89);
        $menu->add('admin', 'settings', 'Etiketler', 'admin.tags', ['fields.manage'], 89);
        $menu->add('admin', 'settings', 'Sözleşmeler', 'admin.agreements', ['agreements.manage'], 89);
        $menu->add('admin', 'settings', 'Sıfatlar', 'admin.affiliation-types', ['affiliations.manage'], 90);
        $menu->add('admin', 'settings', 'Roller ve yetkiler', 'admin.roles', ['roles.manage'], 91);

        $menu->label('admin', 'users', 'panel.users', 'users');
        $menu->add('admin', 'users', 'panel.users', 'admin.users', [1, 2], 10);
        $menu->add('admin', 'users', 'panel.process_logs', 'admin.process-logs', [1], 11);

        // Volunteer figures come from the volunteer module; these fit every association.
        $accounts = fn () => User::where('status', 1);
        $this->dashboard()->stat('Kayıtlı hesap', 'users', fn () => $accounts()->count(), 'admin.users', [1, 2], 12);
        if (! $this->app->make(\App\Modules\ModuleManager::class)->enabled('membership')) {
            $this->dashboard()->stat('Üye', 'id-badge', fn () => $accounts()->whereIn('contact_id', \App\Models\ContactAffiliation::active()->ofType(\App\Models\AffiliationType::MEMBER)->select('contact_id'))->count(), 'admin.users', [1, 2], 9, 'Üye sıfatı olan hesap');
        }
        $this->dashboard()->chart('Aylık yeni kayıt', fn () => Dashboard::monthly($accounts()), 'bar', [1, 2], 20, 'Son 12 ayda her ay açılan hesap sayısı');
    }
}
