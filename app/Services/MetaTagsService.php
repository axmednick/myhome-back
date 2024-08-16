<?php

namespace App\Services;

use App\Helpers\CitySuffix;
use App\Models\City;

class MetaTagsService
{
    public $title;
    public $description;

    public function setMetaTags($query)
    {
        // Query parametrlərini parse edirik
        parse_str($query, $params);

        $result = [
            'title' => 'Ən yeni daşınmaz əmlak satışı və kirayəsi elanları 2024',
            'description' => 'Azərbaycanda ən yeni daşınmaz əmlak satışı və kirayəsi elanları MyHome.az-da! Bakı və rayonlarda ən son mənzil, həyət evi, bağ evi, villa, ofis, obyekt satışı və kirayəsi elanları ilə tanış olmaq üçün veb-sayta keçid edin.',
        ];

        // Müxtəlif query şərtlərinə uyğun meta teqləri təyin etmək
        if (isset($params['room_ids'])) {
            $roomCount = $params['room_ids'];
            $result = [
                'title' => "{$roomCount} otaqlı mənzil qiymətləri, {$roomCount} otaqlı mənzil elanları 2024",
                'description' => "Bakıda {$roomCount} otaqlı mənzillərin kirayə və satışı. {$roomCount} otaqlı evlərin qiyməti, kirayə və satış elanları. Ən son {$roomCount} otaqlı kirayə ev elanları. {$roomCount} otaqlı yeni tikili evlər.",
            ];
        } elseif (isset($params['announcementType']) && $params['announcementType'] == 1) {
            $result = [
                'title' => 'Daşınmaz əmlak satışı. Alqı-satqı elanları 2024',
                'description' => 'Bakı və rayonlarda daşınmaz əmlak alqı-satqısı elanları. İpoteka ilə mənzillər. Ən yeni əmlak elanları. Emlak elanları saytı.',
            ];
        } elseif (isset($params['announcementType']) && $params['announcementType'] == 2) {
            $result = [
                'title' => 'Daşınmaz əmlak kirayəsi. Kirayə elanları 2024',
                'description' => 'Bakı və rayonlarda kirayə evlər. Ən son daşınmaz əmlak, həyət evi, villa, mənzil, ofis, obyekt kirayəsi elanları. Günlük kirayə evlər.',
            ];
        } elseif (isset($params['credit_possible']) && $params['credit_possible'] === 'true') {
            $result = [
                'title' => 'İpoteka ilə mənzil satışı, ipotekaya uyğun çıxarışlı evlər 2024',
                'description' => 'İpoteka ilə sərfəli şərtlərlə satılan mənzilləri burada tapa bilərsiniz. Ipoteka ile evler. Kupçalı evlər. İpotekaya uyğun çıxarışlı mənzil elanları myhome.az-da!',
            ];
        } elseif (isset($params['announcementType']) && $params['announcementType'] == 2 && isset($params['rental_type']) && $params['rental_type'] == 2) {
            $result = [
                'title' => 'Günlük kirayə evlər. Günlük kirayə mənzillər və bağ evləri 2024',
                'description' => 'Günlük kirayəyə verilən mənzil, villa və bağ evləri. Bakıda və rayonlarda günlük kirayə evlər MyHome.az-da! Günlük kirayə evlər.',
            ];
        } elseif (isset($params['client_types_for_rent']) && $params['client_types_for_rent'] == 4) {
            $result = [
                'title' => 'Tələbələr üçün kirayə evlər 2024',
                'description' => 'Tələbələr üçün kirayə ev və mənzil elanları MyHome.az-da! Tələbələr üçün kirayə evlər. Bakıda tələbələrə kirayə evlər verilir. Universitetə yaxın məsafədə kirayə evlər.',
            ];
        } elseif (isset($params['city']) && $params['city'] == 1 && isset($params['propertyType']) && $params['propertyType'] == 6) {
            $result = [
                'title' => 'Bakıda ofis elanları, ofislərin satışı və icarəsi 2024',
                'description' => 'Bakıda ofis elanları, ofislərin icarəsi və satışı. Yeni ofis elanları. Mərkəzə yaxın ofislər, ofis icarəsi elanları. Ən yeni elanlar.',
            ];
        } elseif (isset($params['apartment_type']) && $params['apartment_type'] == 1) {
            $result = [
                'title' => 'Yeni tikili mənzillər 2024',
                'description' => 'Yeni tikililərdə mənzillərin satışı və kirayəsi elanları MyHome.az-da! Ən yeni kirayə və satış elanları. Bakıda və rayonlarda yeni mənzil elanları.',
            ];
        } elseif (isset($params['propertyType']) && $params['propertyType'] == 1) {
            $result = [
                'title' => 'Mənzil alqı-satqısı elanları 2024',
                'description' => 'Bakı və rayonlarda mənzil satışı və kirayəsi elanları. Yeni mənzillərin ən sərfəli qiymətlərlə satışı və kirayəsi. Ən yeni kirayə və satış elanları.',
            ];
        } elseif (isset($params['propertyType']) && $params['propertyType'] == 2) {
            $result = [
                'title' => 'Həyət evi elanları 2024',
                'description' => 'Bakı və rayonlarda həyət evləri. Həyət evi elanları, satışı və kirayəsi. Bakıda həyət evi qiymətləri. Bakıda həyət evi qiyməti. Ən yeni elanlar.',
            ];
        } elseif (isset($params['propertyType']) && $params['propertyType'] == 3) {
            $result = [
                'title' => 'Villa qiymətləri',
                'description' => 'Bakıda villa qiymətləri. Ən yeni villa elanları. Ən lüks villalar, lüks villa elanları və qiymətlər.',
            ];
        } elseif (isset($params['propertyType']) && $params['propertyType'] == 4) {
            $result = [
                'title' => 'Bağ evi, bağ evləri',
                'description' => 'Şəhərdən kənarda bağ evləri. Bağ evi qiymətləri, Bakıda bağ evləri. Rayonlarda bağ evi elanları, ən son elanlar.',
            ];
        } elseif (isset($params['city']) && $params['city'] == 1 && isset($params['propertyType']) && $params['propertyType'] == 2) {
            $city = City::find($params['city']);
            $result = [
                'title' => CitySuffix::cityWithSuffix($city->name). ' Həyət evləri',
                'description' => CitySuffix::cityWithSuffix($city->name). ' həyət evləri. Həyət evi kirayəsi və satışı. Bakıda həyət evi qiymətləri.',
            ];
        } elseif (isset($params['announcementType']) && $params['announcementType'] == 1 && isset($params['propertyType']) && $params['propertyType'] == 1 && isset($params['city']) && $params['city'] == 1) {
            $result = [
                'title' => 'Bakıda mənzil satışı, mənzil alqı-satqısı',
                'description' => 'Bakıda mənzillər, Bakıda mənzil qiymətləri. Yeni tikili mənzillər. Bakıda ucuz mənzillər. Nağd və ipoteka ilə mənzil elanları. Ən yeni elanlar.',
            ];
        } elseif (isset($params['announcementType']) && $params['announcementType'] == 2 && isset($params['client_types_for_rent']) && $params['client_types_for_rent'] == 5) {
            $result = [
                'title' => 'Gənc ailələr üçün sərfəli mənzillər',
                'description' => 'Gənc ailələr üçün sərfəli kirayə evlər. Ən yeni kirayə ev elanları. Bakı və rayonlarda sərfəli şərtlərlə ailələr üçün mənzillər.',
            ];
        } elseif (isset($params['announcementType']) && $params['announcementType'] == 2 && isset($params['city']) && $params['city'] == 1) {
            $result = [
                'title' => 'Bakıda kirayə evlər',
                'description' => 'Bakıda kirayə evlər. Bakıda ucuz kirayə evlər. Bakıda kirayə ev qiymətləri. Bakıda ən yeni kirayə ev elanları myhome.az-da!',
            ];
        }

        // Title və description üçün nəticəni təyin edirik
        $this->title = $result['title'];
        $this->description = $result['description'];
    }

    public function getMetaTags($query)
    {
        $this->setMetaTags($query);

        return [
            'title' => $this->title,
            'description' => $this->description,
        ];
    }
}
