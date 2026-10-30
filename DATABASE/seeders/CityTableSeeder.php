<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $data = [
            [
                'name' => 'Karachi',
                'state_id' => 1,
            ],
            [
                'name' => 'Lahore',
                'state_id' => 2,
            ],
            [
                'name' => 'Faisalabad',
                'state_id' => 2,
            ],
            [
                'name' => 'Rawalpindi',
                'state_id' => 2,
            ],
            [
                'name' => 'Gujranwala',
                'state_id' => 2,
            ],
            [
                'name' => 'Peshawar',
                'state_id' => 3,
            ],
            [
                'name' => 'Multan',
                'state_id' => 2,
            ],
            [
                'name' => 'Saidu Sharif',
                'state_id' => 3,
            ],
            [
                'name' => 'Hyderabad name',
                'state_id' => 1,
            ],
            [
                'name' => 'Islamabad',
                'state_id' => 4,
            ],
            [
                'name' => 'Quetta',
                'state_id' => 5,
            ],
            [
                'name' => 'Bahawalpur',
                'state_id' => 2,
            ],
            [
                'name' => 'Sargodha',
                'state_id' => 2,
            ],
            [
                'name' => 'Sialkot name',
                'state_id' => 2,
            ],
            [
                'name' => 'Sukkur',
                'state_id' => 1,
            ],
            [
                'name' => 'Larkana',
                'state_id' => 1,
            ],
            [
                'name' => 'Chiniot',
                'state_id' => 2,
            ],
            [
                'name' => 'Shekhupura',
                'state_id' => 2,
            ],
            [
                'name' => 'Jhang name',
                'state_id' => 2,
            ],
            [
                'name' => 'Dera Ghazi Khan',
                'state_id' => 2,
            ],
            [
                'name' => 'Gujrat',
                'state_id' => 2,
            ],
            [
                'name' => 'Rahimyar Khan',
                'state_id' => 2,
            ],
            [
                'name' => 'Kasur',
                'state_id' => 2,
            ],
            [
                'name' => 'Mardan',
                'state_id' => 3,
            ],
            [
                'name' => 'Mingaora',
                'state_id' => 3,
            ],
            [
                'name' => 'Nawabshah',
                'state_id' => 1,
            ],
            [
                'name' => 'Sahiwal',
                'state_id' => 2,
            ],
            [
                'name' => 'Mirpur Khas',
                'state_id' => 1,
            ],
            [
                'name' => 'Okara',
                'state_id' => 2,
            ],
            [
                'name' => 'Mandi Burewala',
                'state_id' => 2,
            ],
            [
                'name' => 'Jacobabad',
                'state_id' => 1,
            ],
            [
                'name' => 'Saddiqabad',
                'state_id' => 2,
            ],
            [
                'name' => 'Kohat',
                'state_id' => 3,
            ],
            [
                'name' => 'Muridke',
                'state_id' => 2,
            ],
            [
                'name' => 'Muzaffargarh',
                'state_id' => 2,
            ],
            [
                'name' => 'Khanpur',
                'state_id' => 2,
            ],
            [
                'name' => 'Gojra',
                'state_id' => 2,
            ],
            [
                'name' => 'Mandi Bahauddin',
                'state_id' => 2,
            ],
            [
                'name' => 'Abbottabad',
                'state_id' => 3,
            ],
            [
                'name' => 'Turbat',
                'state_id' => 5,
            ],
            [
                'name' => 'Dadu',
                'state_id' => 1,
            ],
            [
                'name' => 'Bahawalnagar',
                'state_id' => 2,
            ],
            [
                'name' => 'Khuzdar',
                'state_id' => 5,
            ],
            [
                'name' => 'Pakpattan',
                'state_id' => 2,
            ],
            [
                'name' => 'Tando Allahyar',
                'state_id' => 1,
            ],
            [
                'name' => 'Ahmadpur East',
                'state_id' => 2,
            ],
            [
                'name' => 'Vihari',
                'state_id' => 2,
            ],
            [
                'name' => 'Jaranwala',
                'state_id' => 2,
            ],
            [
                'name' => 'New Mirpur',
                'state_id' => 6,
            ],
            [
                'name' => 'Kamalia',
                'state_id' => 2,
            ],
            [
                'name' => 'Kot Addu',
                'state_id' => 2,
            ],
            [
                'name' => 'Nowshera',
                'state_id' => 3,
            ],
            [
                'name' => 'Swabi',
                'state_id' => 3,
            ],
            [
                'name' => 'Khushab',
                'state_id' => 2,
            ],
            [
                'name' => 'Dera Ismail Khan',
                'state_id' => 3,
            ],
            [
                'name' => 'Chaman',
                'state_id' => 5,
            ],
            [
                'name' => 'Charsadda',
                'state_id' => 3,
            ],
            [
                'name' => 'Kandhkot',
                'state_id' => 1,
            ],
            [
                'name' => 'Chishtian',
                'state_id' => 2,
            ],
            [
                'name' => 'Hasilpur',
                'state_id' => 2,
            ],
            [
                'name' => 'Attock Khurd',
                'state_id' => 2,
            ],
            [
                'name' => 'Muzaffarabad',
                'state_id' => 6,
            ],
            [
                'name' => 'Mianwali',
                'state_id' => 2,
            ],
            [
                'name' => 'Jalalpur Jattan',
                'state_id' => 2,
            ],
            [
                'name' => 'Bhakkar',
                'state_id' => 2,
            ],
            [
                'name' => 'Zhob',
                'state_id' => 5,
            ],
            [
                'name' => 'Dipalpur',
                'state_id' => 2,
            ],
            [
                'name' => 'Kharian',
                'state_id' => 2,
            ],
            [
                'name' => 'Mian Channun',
                'state_id' => 2,
            ],
            [
                'name' => 'Bhalwal',
                'state_id' => 2,
            ],
            [
                'name' => 'Jamshoro',
                'state_id' => 1,
            ],
            [
                'name' => 'Pattoki',
                'state_id' => 2,
            ],
            [
                'name' => 'Harunabad',
                'state_id' => 2,
            ],
            [
                'name' => 'Kahror Pakka',
                'state_id' => 2,
            ],
            [
                'name' => 'Toba Tek Singh',
                'state_id' => 2,
            ],
            [
                'name' => 'Samundri',
                'state_id' => 2,
            ],
            [
                'name' => 'Shakargarh',
                'state_id' => 2,
            ],
            [
                'name' => 'Sambrial',
                'state_id' => 2,
            ],
            [
                'name' => 'Shujaabad',
                'state_id' => 2,
            ],
            [
                'name' => 'Hujra Shah Muqim',
                'state_id' => 2,
            ],
            [
                'name' => 'Kabirwala',
                'state_id' => 2,
            ],
            [
                'name' => 'Mansehra',
                'state_id' => 3,
            ],
            [
                'name' => 'Lala Musa',
                'state_id' => 2,
            ],
            [
                'name' => 'Chunian',
                'state_id' => 2,
            ],
            [
                'name' => 'Nankana Sahib',
                'state_id' => 2,
            ],
            [
                'name' => 'Bannu',
                'state_id' => 3,
            ],
            [
                'name' => 'Pasrur',
                'state_id' => 2,
            ],
            [
                'name' => 'Timargara',
                'state_id' => 3,
            ],
            [
                'name' => 'Parachinar',
                'state_id' => 3,
            ],
            [
                'name' => 'Chenab Nagar',
                'state_id' => 2,
            ],
            [
                'name' => 'Gwadar',
                'state_id' => 5,
            ],
            [
                'name' => 'Abdul Hakim',
                'state_id' => 2,
            ],
            [
                'name' => 'Hassan Abdal',
                'state_id' => 2,
            ],
            [
                'name' => 'Tank',
                'state_id' => 3,
            ],
            [
                'name' => 'Hangu',
                'state_id' => 3,
            ],
            [
                'name' => 'Risalpur Cantonment',
                'state_id' => 3,
            ],
            [
                'name' => 'Karak',
                'state_id' => 3,
            ],
            [
                'name' => 'Kundian',
                'state_id' => 2,
            ],
            [
                'name' => 'Umarkot',
                'state_id' => 1,
            ],
            [
                'name' => 'Chitral',
                'state_id' => 3,
            ],
            [
                'name' => 'Dainyor',
                'state_id' => 7,
            ],
            [
                'name' => 'Kulachi',
                'state_id' => 3,
            ],
            [
                'name' => 'Kalat',
                'state_id' => 5,
            ],
            [
                'name' => 'Kotli',
                'state_id' => 6,
            ],
            [
                'name' => 'Gilgit',
                'state_id' => 7,
            ],
            [
                'name' => 'Narowal',
                'state_id' => 2,
            ],
            [
                'name' => 'Khairpur Mir’s',
                'state_id' => 1,
            ],
            [
                'name' => 'Khanewal',
                'state_id' => 2,
            ],
            [
                'name' => 'Jhelum',
                'state_id' => 2,
            ],
            [
                'name' => 'Haripur',
                'state_id' => 3,
            ],
            [
                'name' => 'Shikarpur',
                'state_id' => 1,
            ],
            [
                'name' => 'Rawala Kot',
                'state_id' => 6,
            ],
            [
                'name' => 'Hafizabad',
                'state_id' => 2,
            ],
            [
                'name' => 'Lodhran',
                'state_id' => 2,
            ],
            [
                'name' => 'Malakand',
                'state_id' => 3,
            ],
            [
                'name' => 'Attock name',
                'state_id' => 2,
            ],
            [
                'name' => 'Batgram',
                'state_id' => 3,
            ],
            [
                'name' => 'Matiari',
                'state_id' => 1,
            ],
            [
                'name' => 'Ghotki',
                'state_id' => 1,
            ],
            [
                'name' => 'Naushahro Firoz',
                'state_id' => 1,
            ],
            [
                'name' => 'Alpurai',
                'state_id' => 3,
            ],
            [
                'name' => 'Bagh',
                'state_id' => 6,
            ],
            [
                'name' => 'Daggar',
                'state_id' => 3,
            ],
            [
                'name' => 'Leiah',
                'state_id' => 2,
            ],
            [
                'name' => 'Tando Muhammad Khan',
                'state_id' => 1,
            ],
            [
                'name' => 'Chakwal',
                'state_id' => 2,
            ],
            [
                'name' => 'Badin',
                'state_id' => 1,
            ],
            [
                'name' => 'Lakki',
                'state_id' => 3,
            ],
            [
                'name' => 'Rajanpur',
                'state_id' => 2,
            ],
            [
                'name' => 'Dera Allahyar',
                'state_id' => 5,
            ],
            [
                'name' => 'Shahdad Kot',
                'state_id' => 1,
            ],
            [
                'name' => 'Pishin',
                'state_id' => 5,
            ],
            [
                'name' => 'Sanghar',
                'state_id' => 1,
            ],
            [
                'name' => 'Upper Dir',
                'state_id' => 3,
            ],
            [
                'name' => 'Thatta',
                'state_id' => 1,
            ],
            [
                'name' => 'Dera Murad Jamali',
                'state_id' => 5,
            ],
            [
                'name' => 'Kohlu',
                'state_id' => 5,
            ],
            [
                'name' => 'Mastung',
                'state_id' => 5,
            ],
            [
                'name' => 'Dasu',
                'state_id' => 3,
            ],
            [
                'name' => 'Athmuqam',
                'state_id' => 6,
            ],
            [
                'name' => 'Loralai',
                'state_id' => 5,
            ],
            [
                'name' => 'Barkhan',
                'state_id' => 5,
            ],
            [
                'name' => 'Musa Khel Bazar',
                'state_id' => 5,
            ],
            [
                'name' => 'Ziarat',
                'state_id' => 5,
            ],
            [
                'name' => 'Gandava',
                'state_id' => 5,
            ],
            [
                'name' => 'Sibi',
                'state_id' => 5,
            ],
            [
                'name' => 'Dera Bugti',
                'state_id' => 5,
            ],
            [
                'name' => 'Eidgah',
                'state_id' => 7,
            ],
            [
                'name' => 'Uthal',
                'state_id' => 5,
            ],
            [
                'name' => 'Khuzdar',
                'state_id' => 5,
            ],
            [
                'name' => 'Chilas',
                'state_id' => 7,
            ],
            [
                'name' => 'Panjgur',
                'state_id' => 5,
            ],
            [
                'name' => 'Gakuch',
                'state_id' => 7,
            ],
            [
                'name' => 'Qila Saifullah',
                'state_id' => 5,
            ],
            [
                'name' => 'Kharan',
                'state_id' => 5,
            ],
            [
                'name' => 'Aliabad',
                'state_id' => 7,
            ],
            [
                'name' => 'Awaran',
                'state_id' => 5,
            ],
            [
                'name' => 'Dalbandin',
                'state_id' => 5,
            ],
        ];
        foreach ($data as $item) {
            (new City())->create($item);
        }
    }
}
