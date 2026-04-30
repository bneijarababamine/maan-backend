<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityBeneficiary;
use App\Models\Family;
use App\Models\Orphan;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $admin   = User::where('email', 'admin@charity.mr')->first();
        $manager = User::where('email', 'manager@charity.mr')->first();

        $orphans  = Orphan::where('is_active', true)->get();
        $families = Family::where('is_active', true)->get();

        $activities = [
            [
                'title_ar'         => 'توزيع الزكاة - رمضان 2024',
                'title_fr'         => 'Distribution Zakat - Ramadan 2024',
                'description_ar'   => 'توزيع زكاة الفطر على الأيتام والأسر المحتاجة خلال شهر رمضان المبارك 2024',
                'description_fr'   => 'Distribution de la zakat el fitr aux orphelins et familles nécessiteuses durant le mois de Ramadan 2024',
                'activity_type'    => 'ramadan',
                'beneficiary_type' => 'orphans',
                'activity_date'    => '2024-04-05',
                'total_cost'       => 0,
                'created_by'       => $admin->id,
            ],
            [
                'title_ar'         => 'توزيع الملابس الشتوية 2024',
                'title_fr'         => 'Distribution vêtements d\'hiver 2024',
                'description_ar'   => 'توزيع ملابس شتوية على الأيتام استعداداً لفصل الشتاء',
                'description_fr'   => 'Distribution de vêtements chauds aux orphelins en préparation de la saison hivernale',
                'activity_type'    => 'winter_clothes',
                'beneficiary_type' => 'orphans',
                'activity_date'    => '2024-11-20',
                'total_cost'       => 0,
                'created_by'       => $manager->id,
            ],
            [
                'title_ar'         => 'مساعدة العيد الكبير 2024',
                'title_fr'         => 'Aide Aïd El Kébir 2024',
                'description_ar'   => 'توزيع اللحم وطرود غذائية على الأسر الفقيرة بمناسبة عيد الأضحى المبارك',
                'description_fr'   => 'Distribution de viande et colis alimentaires aux familles pauvres à l\'occasion de l\'Aïd El Kébir',
                'activity_type'    => 'eid_help',
                'beneficiary_type' => 'families',
                'activity_date'    => '2024-06-17',
                'total_cost'       => 0,
                'created_by'       => $admin->id,
            ],
            [
                'title_ar'         => 'دفع رسوم التمدرس 2024-2025',
                'title_fr'         => 'Paiement frais de scolarité 2024-2025',
                'description_ar'   => 'دفع رسوم التسجيل والمستلزمات المدرسية للأيتام الملتحقين بالمدارس',
                'description_fr'   => 'Paiement des frais d\'inscription et fournitures scolaires pour les orphelins scolarisés',
                'activity_type'    => 'school_fees',
                'beneficiary_type' => 'orphans',
                'activity_date'    => '2024-09-02',
                'total_cost'       => 0,
                'created_by'       => $manager->id,
            ],
            [
                'title_ar'         => 'توزيع سلة غذائية - يناير 2025',
                'title_fr'         => 'Distribution panier alimentaire - Janvier 2025',
                'description_ar'   => 'توزيع سلال غذائية شهرية على الأسر المحتاجة',
                'description_fr'   => 'Distribution mensuelle de paniers alimentaires aux familles dans le besoin',
                'activity_type'    => 'food_basket',
                'beneficiary_type' => 'families',
                'activity_date'    => '2025-01-15',
                'total_cost'       => 0,
                'created_by'       => $admin->id,
            ],
            [
                'title_ar'         => 'توزيع زكاة رمضان 2025',
                'title_fr'         => 'Distribution Zakat Ramadan 2025',
                'description_ar'   => 'توزيع الزكاة على الأيتام والأسر الفقيرة خلال شهر رمضان 2025',
                'description_fr'   => 'Distribution de la zakat aux orphelins et familles pauvres durant Ramadan 2025',
                'activity_type'    => 'ramadan',
                'beneficiary_type' => 'general',
                'activity_date'    => '2025-03-10',
                'total_cost'       => 0,
                'created_by'       => $admin->id,
            ],
        ];

        foreach ($activities as $data) {
            $activity = Activity::create($data);

            // Ajouter des bénéficiaires selon le type
            if (in_array($activity->beneficiary_type, ['orphans', 'general'])) {
                $selectedOrphans = $orphans->random(min(8, $orphans->count()));
                $totalOrphans    = 0;
                foreach ($selectedOrphans as $orphan) {
                    $value = $this->getValueForType($activity->activity_type);
                    ActivityBeneficiary::create([
                        'activity_id'      => $activity->id,
                        'beneficiary_type' => 'orphan',
                        'beneficiary_id'   => $orphan->id,
                        'value_received'   => $value,
                    ]);
                    $totalOrphans += $value;
                }
                if ($activity->beneficiary_type === 'orphans') {
                    $activity->update(['total_cost' => $totalOrphans]);
                }
            }

            if (in_array($activity->beneficiary_type, ['families', 'general'])) {
                $selectedFamilies = $families->random(min(6, $families->count()));
                $totalFamilies    = 0;
                foreach ($selectedFamilies as $family) {
                    $value = $this->getValueForType($activity->activity_type);
                    ActivityBeneficiary::create([
                        'activity_id'      => $activity->id,
                        'beneficiary_type' => 'family',
                        'beneficiary_id'   => $family->id,
                        'value_received'   => $value,
                    ]);
                    $totalFamilies += $value;
                }
                if ($activity->beneficiary_type === 'families') {
                    $activity->update(['total_cost' => $totalFamilies]);
                }
            }

            if ($activity->beneficiary_type === 'general') {
                $total = ActivityBeneficiary::where('activity_id', $activity->id)->sum('value_received');
                $activity->update(['total_cost' => $total]);
            }
        }
    }

    private function getValueForType(string $type): float
    {
        return match ($type) {
            'school_fees'   => rand(500, 2000),
            'eid_help'      => rand(500, 1500),
            'food_basket'   => rand(300, 800),
            'winter_clothes'=> rand(400, 1000),
            'ramadan'       => rand(200, 600),
            default         => rand(200, 1000),
        };
    }
}
