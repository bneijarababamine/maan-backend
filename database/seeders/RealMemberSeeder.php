<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class RealMemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            ['full_name' => 'محمد امين سيد الدي',                   'phone' => '46666200', 'gender' => 'male'],
            ['full_name' => 'نوت الزحاف',                            'phone' => '42386468', 'gender' => 'female'],
            ['full_name' => 'خديجة الزحاف',                          'phone' => '27140528', 'gender' => 'female'],
            ['full_name' => 'ساقية خطره',                            'phone' => '36264242', 'gender' => 'female'],
            ['full_name' => 'سلم بوها حيبلا',                        'phone' => '46166807', 'gender' => 'male'],
            ['full_name' => 'سيد احمد محمد الشيخ ايبير',             'phone' => '44020242', 'gender' => 'male'],
            ['full_name' => 'سيد محفوظ بوكه',                        'phone' => '34727270', 'gender' => 'male'],
            ['full_name' => 'محمد قال محفوظ وواء',                   'phone' => '32333385', 'gender' => 'male'],
            ['full_name' => 'عثمان حمود موسى',                       'phone' => '41460851', 'gender' => 'male'],
            ['full_name' => 'بون الله داعيزيه',                      'phone' => '41788453', 'gender' => 'female'],
            ['full_name' => 'ادوم عبدالله عمار',                     'phone' => '46139617', 'gender' => 'male'],
            ['full_name' => 'محمد احمت',                             'phone' => '46654358', 'gender' => 'male'],
            ['full_name' => 'بوب محمد قال وواء',                     'phone' => '42042155', 'gender' => 'male'],
            ['full_name' => 'السالك واد لحمن',                       'phone' => '49484065', 'gender' => 'male'],
            ['full_name' => 'الشيخ عبد الله عبد الرحمن',             'phone' => '46969653', 'gender' => 'male'],
            ['full_name' => 'محمد عالي لوايد',                       'phone' => '37648283', 'gender' => 'male'],
            ['full_name' => 'احمد الزحاف',                           'phone' => '31373497', 'gender' => 'male'],
            ['full_name' => 'باب احمد سالم بكه',                     'phone' => '37337003', 'gender' => 'male'],
            ['full_name' => 'حمادو احمت',                            'phone' => '46517920', 'gender' => 'male'],
            ['full_name' => 'حمادو باب اختيجير',                     'phone' => '43433524', 'gender' => 'male'],
            ['full_name' => 'اعل ادمر البيكم',                       'phone' => '46438505', 'gender' => 'male'],
            ['full_name' => 'تقره سيد البيكم',                       'phone' => '41876463', 'gender' => 'male'],
            ['full_name' => 'محمد حيدر البيكم',                      'phone' => '22357263', 'gender' => 'male'],
            ['full_name' => 'باب احمد البيكم',                       'phone' => '46417393', 'gender' => 'male'],
            ['full_name' => 'باب احمدو احمد لكرع',                   'phone' => '34344312', 'gender' => 'male'],
            ['full_name' => 'سيد احمد البيكم',                       'phone' => '46551156', 'gender' => 'male'],
            ['full_name' => 'بوبكر عبدالله سلكه',                    'phone' => '36311109', 'gender' => 'male'],
            ['full_name' => 'مزمين اعل البيكم',                      'phone' => '41087672', 'gender' => 'male'],
            ['full_name' => 'يسلم مزمين بمر',                        'phone' => '30730130', 'gender' => 'male'],
            ['full_name' => 'الدم محمد محمود اعل لكمال',             'phone' => '46810313', 'gender' => 'male'],
            ['full_name' => 'احمد سالم محمود اعل لكمال',             'phone' => '22814506', 'gender' => 'male'],
            ['full_name' => 'ممدر محمود اعل لكمال',                  'phone' => '46935955', 'gender' => 'male'],
            ['full_name' => 'محمد محمود الداه خطره',                 'phone' => '42332126', 'gender' => 'male'],
            ['full_name' => 'محمد الداه خطره',                       'phone' => '42373700', 'gender' => 'male'],
            ['full_name' => 'السامية الداه خطره',                    'phone' => '48453506', 'gender' => 'female'],
            ['full_name' => 'باب محمد الشيخ محمد الشيخ',             'phone' => '42457878', 'gender' => 'male'],
            ['full_name' => 'السالك محمدو عمار',                     'phone' => '43999985', 'gender' => 'male'],
            ['full_name' => 'حمادو محمدو عمار',                      'phone' => '44445665', 'gender' => 'male'],
            ['full_name' => 'محمد امينو عمار',                       'phone' => '22300522', 'gender' => 'male'],
            ['full_name' => 'سيد محمد احمد سالم',                    'phone' => '47689721', 'gender' => 'male'],
            ['full_name' => 'اسلم محمد احمد سالم',                   'phone' => '46851690', 'gender' => 'male'],
            ['full_name' => 'اعل محمدو الديه',                       'phone' => '38281520', 'gender' => 'male'],
            ['full_name' => 'المخطار الزحاف',                        'phone' => '36494565', 'gender' => 'male'],
            ['full_name' => 'حضرامي محمد اسويلم',                    'phone' => '41515160', 'gender' => 'male'],
            ['full_name' => 'محمد سيد احمد عبدالله',                 'phone' => '36479290', 'gender' => 'male'],
            ['full_name' => 'محمد باب اسويلم',                       'phone' => '48600639', 'gender' => 'male'],
            ['full_name' => 'باب امين سيد احمد عبدالله',             'phone' => '36064707', 'gender' => 'male'],
            ['full_name' => 'ابراهيم سيد احمد عبدالله',              'phone' => '37575451', 'gender' => 'male'],
            ['full_name' => 'ام سيد احمد عبدالله',                   'phone' => '27652755', 'gender' => 'female'],
            ['full_name' => 'محمد سيد احمد عبدالله',                 'phone' => '36010311', 'gender' => 'male'],
            ['full_name' => 'مريم سيد احمد عبدالله',                 'phone' => '20166001', 'gender' => 'female'],
            ['full_name' => 'بيب محمد امين اسويلم',                  'phone' => '27272628', 'gender' => 'male'],
        ];

        foreach ($members as $data) {
            Member::firstOrCreate(
                ['phone' => $data['phone']],
                [
                    'full_name'      => $data['full_name'],
                    'gender'         => $data['gender'],
                    'phone'          => $data['phone'],
                    'whatsapp'       => $data['phone'],
                    'address'        => 'Nouakchott',
                    'profession'     => null,
                    'join_date'      => '2026-04-30',
                    'monthly_amount' => 200,
                    'is_active'      => true,
                    'notes'          => null,
                ]
            );
        }
    }
}
