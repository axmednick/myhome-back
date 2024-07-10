<?php

namespace App\Services;

class MetaTagsService
{

    public $title;
    public $description;


    public function setMetaTags($query){

        $result = match($query) {
            'announcementType=1' => [
                'title' => 'Daşınmaz əmlak satışı. Alqı-satqı elanları 2024',
                'description' => 'Bakı və rayonlarda kirayə evlər. Daşınmaz əmlak alqı-satqısı elanları. İpoteka ilə mənzillər. Ən yeni əmlak elanları. Emlak elanlari sayti.',
            ],
            'announcementType=2' => [
                'title' => 'Daşınmaz əmlak kirayəsi. Kirayə elanları 2024',
                'description' => 'Bakı və rayonlarda kirayə evlər. Ən son daşınmaz əmlak, həyət evi, villa, mənzil, ofis, obyekt kirayəsi elanları. Gunluk kiraye evler.',
            ],
            'credit_possible=true' => [
                'title' => 'İpoteka ilə mənzil satışı, ipotekaya uyğun çıxarışlı evlər 2024',
                'description' => 'İpoteka ilə sərfəli şərtlərlə satılan mənzilləri burada tapa bilərsiniz. Ipoteka ile evler. Kupçali evler. İpotekaya uyğun çıxarışlı mənzil elanları myhome.az-da!',
            ],
            'announcementType=2&rental_type=2' => [
                'title' => 'Günlük kirayə evlər. Günlük kirayə mənzillər və bağ evləri 2024',
                'description' => 'Günlük kirayəyə verilən mənzil, villa və bağ evləri. Bakıda və rayonlarda günlük kirayə evlər MyHome.az-da! Gunluk kiraye evler.',
            ],
            'client_types_for_rent=4' => [
                'title' => 'Tələbələr üçün kirayə evlər 2024',
                'description' => 'Tələbələr üçün kirayə ev və mənzil elanları MyHome.az-da! Telebeler ucun kiraye evler. Bakida telebelere kiraye evler verilir. Universitetə yaxın məsafədə kirayə evlər.',
            ],
            'room_ids=2' => [
                'title' => '2 otaqlı mənzil qiymətləri, 2 otaqlı mənzil elanları 2024',
                'description' => 'Bakıda 2 otaqlı mənzillərin kirayə və satışı. 2 otaqli evlerin qiymeti, kiraye ve satis elanları. En son 2 otaqli kiraye ev elanlari. 2 otaqli yeni tikili evler.',
            ],
            'city=1&propertyType=6' => [
                'title' => 'Bakıda ffis elanları, ofislərin satışı və icarəsi 2024',
                'description' => 'Bakıda ofis elanları, ofislərin icarəsi və satışı. Yeni ofis elanlari. Merkeze yaxin ofisler, ofis icaresi elanlari. En yeni elanlar.',
            ],
            'apartment_type=1' => [
                'title' => 'Yeni tikili mənzillər 2024',
                'description' => 'Yeni tikililərdə mənzillərin satışı və kirayəsi elanları MyHome.az-da! En yeni kiraye ve satis elanlari. Bakıda və rayonlarda yeni mənzil elanları.',
            ],
            'propertyType=1' => [
                'title' => 'Mənzil alqı-satqısı elanları 2024',
                'description' => 'Bakı və rayonlarda mənzil satışı və kirayəsi elanları. Yeni mənzillərin ən sərfəli qiymətlərlə satışı və kirayəsi. En yeni kiraye ve satis elanlari.',
            ],
            'propertyType=2' => [
                'title' => 'Həyət evi elanları 2024',
                'description' => 'Bakı və rayonlarda həyət evləri. Heyet evi elanlari, satisi ve kirayesi. Bakida heyet evi qiymetleri. Bakıda heyet evi qiymeti. En yeni elanlar.',
            ],
            'propertyType=1&city=1' => [
                'title' => 'Bakıda daşınmaz əmlak satışı',
                'description' => 'Bakida menzil satisi, heyet evi elanlari, alqi satqi elanlari. Bakida menzil qiymetleri. En yeni satis elanlari.',
            ],
            'announcementType=1&propertyType=1&city=1' => [
                'title' => 'Bakıda mənzil satışı, mənzil alqı-satısı',
                'description' => 'Bakida menziller, Bakida menzi qiymetleri. Yeni tikili menziller. Bakida ucuz menziller. Nağd və ipoteka ilə mənzil elanları. En yeni elanlar.',
            ],
            'announcementType=2&city=1' => [
                'title' => 'Bakıda kirayə evlər',
                'description' => 'Bakida kiraye evler. Bakida ucuz kiraye evlər. Bakida kiraye ev qiymetleri. Bakida en yenu kiraye ev elanlari myhome.az-da!',
            ],
            'announcementType=2&client_types_for_rent=5' => [
                'title' => 'Gənc ailələr üçün sərfəli mənzillər',
                'description' => 'Gənc ailələr üçün sərfəli kirayə evlər. En yeni kiraye ev elanlari. Baki ve rayonlarda serfeli sertlerle aileler ucun menziller.',
            ],
            'propertyType=3' => [
                'title' => 'Villa qiymətləri',
                'description' => 'Bakıda villa qiymətləri. En yeni villa elanlari. En luks villalar, lüks villa elanlari ve qiymetler.',
            ],
            'propertyType=4' => [
                'title' => 'Bağ evi, bağ evləri',
                'description' => 'Şəhərdən kənarda bağ evləri. Bag evi qiymetleri, Bakida bag evleri. Rayonlarda bag evi elanlari, en son elanlar.',
            ],
            'city=1&propertyType=2' => [
                'title' => 'Həyət evləri / Bakı',
                'description' => 'Bütün şəhər və rayonlarda həyət evləri. Heyet evi kirayəsi və satışı. Bakıda heyet evi qiymetleri.',
            ],
            default => [
                'title' => 'Ən yeni daşınmaz əmlak satışı və kirayəsi elanları 2024',
                'description' => 'Azərbaycanda ən yeni daşınmaz əmlak satışı və kirayəsi elanları MyHome.az-da! Bakı və rayonlarda ən son mənzil, həyət evi, bağ evi, villa, ofis, obyekt satışı və kirayəsi elanları ilə tanış olmaq üçün veb-sayta keçid edin.',
            ],
        };



        $this->title=$result['title'];
        $this->description=$result['description'];
    }

    public function getMetaTags($query){
        $this->setMetaTags($query);

        return [
            'title'=>$this->title,
            'description'=>$this->description
        ];
    }

}
