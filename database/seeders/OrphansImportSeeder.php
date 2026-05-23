<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Regroupe les orphelins par NOM DU PÈRE (partie fixe du full_name).
 * Pour chaque groupe, le tuteur retenu est celui dont le téléphone
 * ressemble le plus à un numéro mauritanien (8 chiffres, commence par 2/3/4).
 */
class OrphansImportSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('orphans')->truncate();
        DB::table('guardians')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ── TUTEURS ────────────────────────────────────────────────────────
        // Clé utilisée dans $orphans : 'guardian' => clé ici
        $guardians = [
            // clé                              => [name, phone, address]
            'amat'       => ['أمات الشنظورة',                         '20775400',  'Nouakchott'],
            'layla'      => ['ليلى محمد علال',                        '44978383',  'Nouakchott'],  // père: سيد الشيخ باي علال
            'nousho'     => ['النوشو',                                 '41600287',  'Nouakchott'],  // père: جمال
            'llab'       => ['للب',                                    '47326115',  'Tidjikdja'],   // père: داتي ممود ميني
            'akhbarha'   => ['اخبارها الشيخ سيد احمد حيبلا',          '36019933',  'Nouakchott'],  // père: اعل الشيخ
            'salakha'    => ['السالكه بوي احمد بوكطاية',               '48470894',  'Nouadhibou'],  // père: محمد لمات اعليات (tel temp — même base que akhbarha)
            'salama'     => ['السالمة',                                '46964241',  'Nouakchott'],  // père: محفوظ الحاج اخنيجير
            'sultanah'   => ['السلطانة سيد محمد بكه',                  '22610437',  'Nouakchott'],
            'mammi'      => ['المامي',                                 '47756101',  'Nouakchott'],
            'bbb'        => ['ببب',                                    '6666',      'Tamchekett'],
            'ttt'        => ['تتت',                                    '94335',     'Tamchekett'],   // père: سيد سالم
            'lll'        => ['للل',                                    '677777',    'Nouakchott'],   // père: مدي محمد اعل
            'tahiya'     => ['تحية الشيخ اعلاتي',                     '44026127',  'Nouakchott'],  // père: سيد اعليات
            'tout'       => ['توت المامي',                             '27942665',  'Nouakchott'],  // père: باب سيد حمً احمد لكحل
            'khadja'     => ['خدجة',                                   '43628874',  'Néma'],         // père: اعل ابعير
            'khadijah'   => ['خديجه',                                  '26717141',  'Nouakchott'],  // père: محمد لمين احمد لكرع
            'rqya'       => ['رقية زيني اوداعه',                       '49813056',  'Tamchekett'],  // père: سيداتي محمدو سلكه
            'zaynab_mk'  => ['زينب محمد سيد محمد بكه',                 '31220020',  'Nouakchott'],  // père: التوراد
            'zaynab_lm'  => ['زينب لمات محمد اعل',                     '47448946',  'Nouakchott'],
            'zaynab_ax'  => ['زينب محمد لمين اخنيجير',                 '46861641',  'Nouakchott'],  // père: حماده الحاج اخنيجير
            'chakara'    => ['شكارة هيبه',                             '5557777',   'Kiffa'],
            'acha'       => ['عاشه محمدالسالك سيدالمختار تتار',        '36678240',  'Kiffa'],        // père: محمد محمود سيد المختار تتار
            'fatima_hmd' => ['فاطمة سيد احمد حمادي الدِي',             '48848685',  'Tamchekett'],  // père: سيد احمد حمادي الدي
            'mhm_shk'    => ['محمد محمد الشيخ',                        '32727557',  'Nouakchott'],  // père: محمد محمد الشيخ
            'fal'        => ['فال',                                    '77766666',  'Tamchekett'],
            'qaya'       => ['قاية اعل باب',                           '49278312',  'Tamchekett'],
            'kanbura'    => ['كنبورة محمد محمود اعل لكحل',             '49545120',  'Nouakchott'],
            'mali'       => ['مالي',                                   '77877877',  'Nouakchott'],
            'maryam_b'   => ['مريم البينان',                           '42554543',  'Nouakchott'],  // père: احمد ابوه
            'maryam_s'   => ['مريم السالك عبدوه',                      '26353240',  'Aïoun'],        // père: سيد محمد بوبكر
            'maryam_bn'  => ['مريم الشيخ بنمو',                        '22595776',  'Nouakchott'],  // père: محمد لمين عبدالله بنمو
            'maryamh'    => ['مريمه اكليب',                            '20838276',  'Aïoun'],        // père: محمدو لمرابط عَمار
            'mana'       => ['منة',                                    '32178726',  'Nouakchott'],  // père: حماده معه موسى
            'manynh'     => ['منينة سيد احمد',                         '36400595',  'Nouakchott'],  // père: النيني محمد محمود
            'mila'       => ['ميلة ختوري محمد اعل',                    '48448277',  'Nouakchott'],  // plusieurs pères
            'nura'       => ['نورة عبدالرزاق',                         '20492555',  'Nouakchott'],
            'yslm'       => ['يسلم محمد احمت',                         '46553153',  'Tamchekett'],
            'aaaaب'      => ['ااааب',                                   '688654',    'Kiffa'],
        ];

        $map = [];
        foreach ($guardians as $key => $g) {
            $map[$key] = DB::table('guardians')->insertGetId([
                'name'       => $g[0],
                'phone'      => $g[1],
                'address'    => $g[2],
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ── ORPHELINS ──────────────────────────────────────────────────────
        // [full_name, birth_date, gender, school, grade, is_active, notes, guardian_key]
        $orphans = [
            // ── père : اسويلم عُمار ──────────────────────────────────────
            ['افيلة ودًو اسويلم عُمار',                   '2007-12-31','female',null,null,                      1,null,       'amat'],
            ['سمية ودًو اسويلم عُمار',                    '2017-12-31','female',null,null,                      1,null,       'amat'],

            // ── père : سيد الشيخ باي علال ────────────────────────────────
            ['الون سيد الشيخ باي علال',                   '2008-12-31','female',null,null,                      1,null,       'layla'],
            ['جدو سيد الشيخ باي علال',                    '2010-12-31','male',  null,null,                      1,null,       'layla'],
            ['محمد سيد الشيخ باي علال',                   '2013-12-31','male',  null,null,                      1,null,       'layla'],
            ['الشفاء سيد الشيخ باي علال',                 '2007-12-31','female',null,null,                      1,null,       'layla'],
            ['النينة سيد الشيخ باي علال',                 '2017-12-31','female',null,null,                      1,null,       'layla'],

            // ── père : جمال (علال) ───────────────────────────────────────
            ['تكبر جمال علال',                             '2020-12-31','male',  null,null,                      1,null,       'nousho'],
            ['ديدة جمال علال',                             '2015-12-31','female',null,null,                      1,null,       'nousho'],
            ['عيشه جمال محفوظ علال',                      '2014-12-31','male',  null,null,                      1,null,       'nousho'],
            ['باب جمال علال',                              '2021-12-31','male',  null,null,                      1,null,       'nousho'],

            // ── père : داتي ممود ميني ────────────────────────────────────
            ['ميني داتي ممود ميني',                        '2016-12-31','male',  null,null,                      1,null,       'llab'],
            ['ام كلثوم داتي ممود ميني',                   '2018-12-31','male',  null,null,                      1,null,       'llab'],
            ['خدجة داتي ممود ميني',                        '2011-12-31','female',null,null,                      1,null,       'llab'],
            ['ابنيجاره داتي ممود ميني',                   '2011-12-31','male',  null,null,                      1,null,       'llab'],

            // ── père : سيد احمد بوبكر ────────────────────────────────────
            ['اهل سيد احمد بوبكر',                         '2008-12-31','male',  null,null,                      1,null,       'aaaaب'],

            // ── père : اعل الشيخ ─────────────────────────────────────────
            ['محمد اعل الشيخ',                             '2020-12-31','male',  null,null,                      1,null,       'akhbarha'],
            ['عمرانه اعل الشيخ',                           '2012-09-16','female',null,null,                      1,null,       'akhbarha'],
            ['فاطمة اعل الشيخ',                            '2016-06-28','female',null,null,                      1,null,       'akhbarha'],

            // ── père : محمد لمات اعليات ──────────────────────────────────
            ['الحسن  محمد لمات اعليات',                   '2009-12-31','male',  null,null,                      1,null,       'salakha'],
            ['الحسين  محمد لمات اعليات',                  '2009-12-31','male',  null,null,                      1,null,       'salakha'],
            ['امنة محمد لمات اعليات',                      '2009-12-31','male',  null,null,                      1,null,       'salakha'],
            ['محفوظ  محمد لمات اعليات',                   '2020-12-31','male',  null,null,                      1,null,       'salakha'],
            ['سلمه محمد لمات اعليات',                      '2024-05-12','male',  null,null,                      1,null,       'salakha'],

            // ── père : محفوظ الحاج اخنيجير ───────────────────────────────
            ['عيشه محفوظ الحاج اخنيجير',                  '2016-12-31','female',null,null,                      1,null,       'salama'],
            ['الحاج محفوظ الحاج اخنيجير',                 '2013-12-31','male',  null,null,                      1,null,       'salama'],

            // ── individuel ────────────────────────────────────────────────
            ['السلطانة سيد محمد بكه',                      '2016-05-12','male',  null,null,                      1,null,       'sultanah'],
            ['سيد احمد بوكجه',                             '2019-05-12','male',  null,null,                      1,null,       'mammi'],
            ['رقية يحظيه مودي',                            '2007-12-31','male',  null,null,                      1,null,       'bbb'],

            // ── père : سيد سالم ──────────────────────────────────────────
            ['لالة سيد سالم',                              '2007-12-31','male',  null,null,                      1,null,       'ttt'],
            ['سيد احمد سيد سالم',                          '2009-12-31','male',  null,null,                      1,null,       'ttt'],

            // ── père : مدي محمد اعل ──────────────────────────────────────
            ['عيشة مدي محمد اعل',                          '2008-12-31','female',null,null,                      1,null,       'lll'],
            ['ادرجالها  مدي محمد اعل',                    '2005-12-31','female',null,null,                      1,null,       'lll'],
            ['عبد المؤمن  مدي محمد اعل',                  '2003-12-31','male',  null,null,                      1,null,       'lll'],
            ['قاية  مدي محمد اعل',                         '1999-12-31','female',null,null,                      1,null,       'lll'],

            // ── père : سيد اعليات ────────────────────────────────────────
            ['الحضرامي سيد اعليات',                        '2010-12-31','male',  null,null,                      1,null,       'tahiya'],
            ['الشيخ سيد اعليات',                           '2012-12-31','male',  null,null,                      1,null,       'tahiya'],

            // ── père : باب سيد حمً احمد لكحل ─────────────────────────────
            ['اعل باب سيد حمً احمد لكحل',                 '2012-12-31','male',  null,null,                      1,null,       'tout'],
            ['اندي باب سيد حمً احمد لكحل',                '2007-12-31','female',null,null,                      1,null,       'tout'],
            ['محمد باب سيد حمً احمد لكحل',                '2009-12-31','male',  null,null,                      1,null,       'tout'],

            // ── père : اعل ابعير ─────────────────────────────────────────
            ['الناجي اعل ابعير',                           '2020-12-31','male',  null,null,                      1,null,       'khadja'],
            ['محمد اعل ابعير',                             '2019-05-10','male',  null,null,                      1,null,       'khadja'],

            // ── père : محمد لمين احمد لكرع ───────────────────────────────
            ['احمدو محمد لمين احمد لكرع',                  '2013-12-31','male',  null,null,                      1,null,       'khadijah'],
            ['فاطمة محمد لمين احمد لكرع',                  '2019-09-12','male',  null,null,                      1,null,       'khadijah'],

            // ── père : سيداتي محمدو سلكه ─────────────────────────────────
            ['مريم سيداتي محمدو سلكه',                     '2012-12-31','male',  null,null,                      1,null,       'rqya'],
            ['احمدو سيداتي محمدو سلكه',                    '2020-05-12','male',  null,null,                      1,null,       'rqya'],
            ['محمد سيداتي محمدو سلكه',                     '2017-12-31','male',  null,null,                      1,null,       'rqya'],

            // ── père : التوراد ────────────────────────────────────────────
            ['محجوبة التوراد',                             '2016-12-31','female',null,null,                      1,null,       'zaynab_mk'],
            ['بمب التوراد',                                '2019-12-31','male',  null,null,                      1,null,       'zaynab_mk'],

            // ── individuel ────────────────────────────────────────────────
            ['زينب لمات محمد اعل',                         '2016-03-12','male',  null,null,                      1,null,       'zaynab_lm'],
            ['زينب محمد سيد محمد بكه',                     '2015-08-15','male',  null,null,                      1,null,       'zaynab_mk'],

            // ── père : حماده الحاج اخنيجير ───────────────────────────────
            ['الحاج حماده الحاج اخنيجير',                  '2011-12-31','male',  null,null,                      1,null,       'zaynab_ax'],
            ['النينة حماده الجاح اخنيجير',                 '2002-12-31','female',null,null,                      1,null,       'zaynab_ax'],
            ['توت حماده الحاج اخنيجير',                    '2007-12-31','male',  null,null,                      1,null,       'zaynab_ax'],

            // ── individuel ────────────────────────────────────────────────
            ['اشيب الساموري اعليات',                       '2009-12-31','male',  null,null,                      1,null,       'chakara'],

            // ── père : محمد محمود سيد المختار تتار ───────────────────────
            ['النعمه محمد محمود سيد المختار تتار',          '2022-10-12','male',  null,null,                      1,null,       'acha'],
            ['سيد المختار محمد محمود سيد المختار. تتار',   '2019-06-08','male',  'كيفه','السنة السادسة ابتدائية',1,null,       'acha'],
            ['محمدلمين محمد محمود سيد المختار تتار',        '2018-09-23','male',  'كيفه','السنة اولى اعدادية',   1,null,       'acha'],

            // ── père : سيد احمد حمادي الدي ───────────────────────────────
            ['رضى',                                        '2019-12-12','female',null,null,                      1,null,       'fatima_hmd'],
            ['فاطمة سيد احمد حمادي الدِي',                '2016-12-31','female',null,null,                      1,null,       'fatima_hmd'],

            // ── père : محمد محمد الشيخ ───────────────────────────────────
            ['ام كلثوم محمد محمد الشيخ',                   '2025-10-15','female',null,null,                      1,null,       'mhm_shk'],
            ['مريم محمد محمد الشيخ',                       '2024-09-11','female',null,null,                      1,null,       'mhm_shk'],
            ['خدجة محمد محمد الشيخ',                       '1994-12-31','female',null,null,                      1,null,       'mhm_shk'],

            // ── individuel ────────────────────────────────────────────────
            ['سيد محمد فال',                               '2009-12-31','male',  null,null,                      1,null,       'fal'],
            ['حياتي الفضيل محمد الديَه',                   '2016-12-31','male',  null,null,                      1,null,       'qaya'],
            ['فاطمة محمدو محمد الشيخ',                     '2020-12-31','female',null,null,                      1,null,       'kanbura'],
            ['هاده محمدالسالك الناجي',                      '2020-07-22','male',  null,null,                      1,null,       'mali'],

            // ── père : احمد ابوه ─────────────────────────────────────────
            ['سيد احمد ابوه',                              '2009-12-31','male',  null,null,                      1,null,       'maryam_b'],
            ['طعمازة احمد ابوه',                           '2012-12-31','male',  null,null,                      1,null,       'maryam_b'],
            ['محمد لمين احمد ابوه',                         '2017-12-31','male',  null,null,                      1,null,       'maryam_b'],
            ['محمدو احمد ابوه',                            '2015-12-31','male',  null,null,                      1,null,       'maryam_b'],

            // ── père : سيد محمد بوبكر ────────────────────────────────────
            ['احمد سالم سيد محمد بوبكر',                   '2017-12-31','male',  null,null,                      1,null,       'maryam_s'],
            ['زينب سيد محمد بوبكر',                        '2020-12-31','male',  null,null,                      1,null,       'maryam_s'],
            ['سَلَمْ سيد محمد بوبكر',                      '2012-12-31','male',  null,null,                      1,null,       'maryam_s'],
            ['سلْمه سيد محمد بوبكر',                       '2013-12-31','male',  null,null,                      1,null,       'maryam_s'],

            // ── père : محمد لمين عبدالله بنمو ────────────────────────────
            ['عبدالله محمد لمين عبدالله بنمو',              '2016-12-31','male',  null,null,                      1,null,       'maryam_bn'],
            ['منة محمد لمين عبدالله بنمو',                  '2013-12-31','female',null,null,                      1,null,       'maryam_bn'],

            // ── père : محمدو لمرابط عَمار ────────────────────────────────
            ['سيد محمدو لمرابط عَمار',                     '2014-12-31','male',  null,null,                      1,null,       'maryamh'],
            ['لمرابط محمدو لمرابط عَمار',                  '2010-12-31','male',  null,null,                      1,null,       'maryamh'],

            // ── père : حماده معه موسى ────────────────────────────────────
            ['الطاهرة حماده معه موسى',                      '2020-12-31','female',null,null,                      1,null,       'mana'],
            ['بوبكر الصديق حماده معه موسى',                '2016-12-31','male',  null,null,                      1,null,       'mana'],
            ['بوننه حماده معه موسى',                       '2018-12-31','male',  null,null,                      1,null,       'mana'],
            ['خطاري حماده معه موسى',                       '2012-12-31','male',  null,null,                      1,null,       'mana'],
            ['مامه حماده معه موسى',                        '2015-12-31','female',null,null,                      1,null,       'mana'],

            // ── père : النيني محمد محمود ─────────────────────────────────
            ['تتاح النيني محمد محمود',                      '2017-12-31','male',  null,null,                      1,null,       'manynh'],
            ['تيتي النيني محمد محمود',                      '2019-12-31','female',null,null,                      1,null,       'manynh'],

            // ── tutrice ميلة — plusieurs pères ───────────────────────────
            ['ام الخير ابراهيم',                           '2007-12-31','female',null,null,                      1,'41179859',  'mila'],
            ['سيد احمد محمد محمود',                         '2015-12-31','male',  null,null,                      1,null,       'mila'],
            ['فاطمة محمد محمود',                           '2010-12-31','female',null,null,                      1,null,       'mila'],

            // ── individuels ───────────────────────────────────────────────
            ['اهل أمين احمد داده',                          '2019-04-12','male',  null,null,                      1,null,       'nura'],
            ['انوها بمب محمد احمت',                         '2019-09-12','female',null,null,                      1,null,       'yslm'],
        ];

        foreach ($orphans as [$name, $bdate, $gender, $school, $grade, $active, $notes, $gkey]) {
            DB::table('orphans')->insert([
                'full_name'   => $name,
                'birth_date'  => $bdate,
                'gender'      => $gender,
                'school_name' => $school,
                'grade'       => $grade,
                'guardian_id' => $map[$gkey],
                'is_active'   => $active,
                'notes'       => $notes,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        $this->command->info('✓ ' . count($guardians) . ' tuteurs, ' . count($orphans) . ' orphelins importés.');
    }
}
