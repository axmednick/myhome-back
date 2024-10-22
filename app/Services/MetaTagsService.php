<?php

namespace App\Services;

use App\Helpers\CitySuffix;
use App\Models\City;
use App\Models\MetroStation;

class MetaTagsService
{
    public $title;
    public $description;

    public function setMetaTags($query)
    {

        // Query parametrlərini parse edirik
        parse_str($query, $params);

        if (isset($params['city'])){
            $city = City::find($params['city']);
            $cityNameWithSuffix = CitySuffix::cityWithSuffix($city->name);
        }



        $result = [
            'title' => 'Ən yeni daşınmaz əmlak satışı və kirayəsi elanları 2024',
            'description' => 'Azərbaycanda ən yeni daşınmaz əmlak satışı və kirayəsi elanları MyHome.az-da! Bakı və rayonlarda ən son mənzil, həyət evi, bağ evi, villa, ofis, obyekt satışı və kirayəsi elanları ilə tanış olmaq üçün veb-sayta keçid edin.',
        ];

        // Müxtəlif query şərtlərinə uyğun meta teqləri təyin etmək
        if (isset($params['room_ids']) && count($params) == 1) {
            $roomCount = $params['room_ids'];
            $result = [
                'title' => "{$roomCount} otaqlı mənzil qiymətləri, {$roomCount} otaqlı mənzil elanları 2024",
                'description' => "Bakıda {$roomCount} otaqlı mənzillərin kirayə və satışı. {$roomCount} otaqlı evlərin qiyməti, kirayə və satış elanları. Ən son {$roomCount} otaqlı kirayə ev elanları. {$roomCount} otaqlı yeni tikili evlər.",
            ];
        } elseif (isset($params['announcementType']) && $params['announcementType'] == 1 && count($params) == 1) {
            $result = [
                'title' => 'Daşınmaz əmlak satışı. Alqı-satqı elanları 2024',
                'description' => 'Bakı və rayonlarda daşınmaz əmlak alqı-satqısı elanları. İpoteka ilə mənzillər. Ən yeni əmlak elanları. Emlak elanları saytı.',
            ];
        } elseif (isset($params['announcementType']) && $params['announcementType'] == 2 && count($params) == 1) {
            $result = [
                'title' => 'Daşınmaz əmlak kirayəsi. Kirayə elanları 2024',
                'description' => 'Bakı və rayonlarda kirayə evlər. Ən son daşınmaz əmlak, həyət evi, villa, mənzil, ofis, obyekt kirayəsi elanları. Günlük kirayə evlər.',
            ];
        } elseif (isset($params['credit_possible']) && $params['credit_possible'] === 'true' && count($params) == 1) {
            $result = [
                'title' => 'İpoteka ilə mənzil satışı, ipotekaya uyğun çıxarışlı evlər 2024',
                'description' => 'İpoteka ilə sərfəli şərtlərlə satılan mənzilləri burada tapa bilərsiniz. Ipoteka ile evler. Kupçalı evlər. İpotekaya uyğun çıxarışlı mənzil elanları myhome.az-da!',
            ];
        } elseif (isset($params['announcementType']) && $params['announcementType'] == 2 && isset($params['rental_type']) && $params['rental_type']) {
            $result = [
                'title' => 'Günlük kirayə evlər. Günlük kirayə mənzillər və bağ evləri 2024',
                'description' => 'Günlük kirayəyə verilən mənzil, villa və bağ evləri. Bakıda və rayonlarda günlük kirayə evlər MyHome.az-da! Günlük kirayə evlər.',
            ];
        } elseif (isset($params['client_types_for_rent']) && $params['client_types_for_rent'] == 4) {
            $result = [
                'title' => 'Tələbələr üçün kirayə evlər 2024',
                'description' => 'Tələbələr üçün kirayə ev və mənzil elanları MyHome.az-da! Tələbələr üçün kirayə evlər. Bakıda tələbələrə kirayə evlər verilir. Universitetə yaxın məsafədə kirayə evlər.',
            ];
        }
        ///Tags for city


        elseif (isset($params['city']) && count($params) == 1 ) {
            $result = [
                'title' => "{$cityNameWithSuffix} mənzillər, həyət evləri,villa və bağ evləri",
                'description' => "{$cityNameWithSuffix} ev elanları, obyektlərin satışı və icarəsi. {$cityNameWithSuffix} əmlak",
            ];
        }

        elseif (isset($params['city'])  && isset($params['propertyType']) && $params['propertyType'] == 1 ) {

            $result = [
                'title' => $cityNameWithSuffix . ' Evlər və Mənzillər ',
                'description' => $cityNameWithSuffix . ' menziller. menzil və ev kirayəsi və satışı. '.$cityNameWithSuffix.' menzil  qiymətləri.',
            ];
        }

        elseif (isset($params['city'])  && isset($params['propertyType']) && $params['propertyType'] == 2 ) {

            $result = [
                'title' => $cityNameWithSuffix . ' Həyət evləri',
                'description' => $cityNameWithSuffix . ' həyət evləri. Həyət evi kirayəsi və satışı. '.$cityNameWithSuffix.' həyət evi qiymətləri.',
            ];
        }

        elseif (isset($params['city'])  && isset($params['propertyType']) && $params['propertyType'] == 6 ) {
            $result = [
                'title' => "{$cityNameWithSuffix} ofis elanları, ofislərin satışı və icarəsi 2024",
                'description' => "{$cityNameWithSuffix} ofis elanları, ofislərin icarəsi və satışı. Yeni ofis elanları. Mərkəzə yaxın ofislər, ofis icarəsi elanları. Ən yeni elanlar.",
            ];
        }

        elseif (isset($params['city']) && isset($params['propertyType']) && $params['propertyType'] == 3 ) {
            $result = [
                'title' => "{$cityNameWithSuffix} villa elanları, villaların satışı və kirayəsi 2024",
                'description' => "{$cityNameWithSuffix} villa elanları, lüks villa satışı və kirayəsi. Ən yeni villa elanları. {$cityNameWithSuffix} elit villalar, villa icarəsi.",
            ];
        }


        elseif (isset($params['city']) && isset($params['propertyType']) && $params['propertyType'] == 4 ) {
            $result = [
                'title' => "{$cityNameWithSuffix} bağ evi elanları, bağ evlərinin satışı və kirayəsi 2024",
                'description' => "{$cityNameWithSuffix} bağ evi elanları, bağ evlərinin satışı və kirayəsi. Şəhərdən kənarda bağ evləri. Ən yeni bağ evi elanları.",
            ];
        } elseif (isset($params['city']) && isset($params['propertyType']) && $params['propertyType'] == 5 ) {
            $result = [
                'title' => "{$cityNameWithSuffix} torpaq elanları,{$cityNameWithSuffix} torpaq qiymətləri torpaqların satışı 2024",
                'description' => "{$cityNameWithSuffix} torpaq elanları. Ən yeni torpaq satışı elanları. {$cityNameWithSuffix} bağ və əkin sahələrinin satışı.",
            ];
        } elseif (isset($params['city']) && isset($params['propertyType']) && $params['propertyType'] == 7 ) {
            $result = [
                'title' => "{$cityNameWithSuffix} obyekt elanları, obyektlərin satışı və icarəsi 2024",
                'description' => "{$cityNameWithSuffix} obyekt elanları, obyektlərin satışı və icarəsi. {$cityNameWithSuffix} ən yeni kommersiya obyektləri.",
            ];
        }

        //Tags for city end
        elseif (isset($params['apartment_type']) && $params['apartment_type'] == 1 && count($params) == 1) {
            $result = [
                'title' => 'Yeni tikili mənzillər 2024',
                'description' => 'Yeni tikililərdə mənzillərin satışı və kirayəsi elanları MyHome.az-da! Ən yeni kirayə və satış elanları. Bakıda və rayonlarda yeni mənzil elanları.',
            ];
        } elseif (isset($params['propertyType']) && $params['propertyType'] == 1 && count($params) == 1) {
            $result = [
                'title' => 'Mənzil alqı-satqısı elanları 2024',
                'description' => 'Bakı və rayonlarda mənzil satışı və kirayəsi elanları. Yeni mənzillərin ən sərfəli qiymətlərlə satışı və kirayəsi. Ən yeni kirayə və satış elanları.',
            ];
        } elseif (isset($params['propertyType']) && $params['propertyType'] == 2 && count($params) == 1) {
            $result = [
                'title' => 'Həyət evi elanları 2024',
                'description' => 'Bakı və rayonlarda həyət evləri. Həyət evi elanları, satışı və kirayəsi. Bakıda həyət evi qiymətləri. Bakıda həyət evi qiyməti. Ən yeni elanlar.',
            ];
        } elseif (isset($params['propertyType']) && $params['propertyType'] == 3 && count($params) == 1) {
            $result = [
                'title' => 'Villa qiymətləri',
                'description' => 'Bakıda villa qiymətləri. Ən yeni villa elanları. Ən lüks villalar, lüks villa elanları və qiymətlər.',
            ];
        } elseif (isset($params['propertyType']) && $params['propertyType'] == 4 && count($params) == 1) {
            $result = [
                'title' => 'Bağ evi, bağ evləri',
                'description' => 'Şəhərdən kənarda bağ evləri. Bağ evi qiymətləri, Bakıda bağ evləri. Rayonlarda bağ evi elanları, ən son elanlar.',
            ];
        }  elseif (isset($params['announcementType']) && $params['announcementType'] == 1 && isset($params['propertyType']) && $params['propertyType'] == 1 && isset($params['city']) && count($params) == 3) {
            $result = [
                'title' => "{$cityNameWithSuffix} mənzil satışı, mənzil alqı-satqısı",
                'description' => "{$cityNameWithSuffix} mənzillər, {$cityNameWithSuffix} mənzil qiymətləri. Yeni tikili mənzillər. {$cityNameWithSuffix} ucuz mənzillər. Nağd və ipoteka ilə mənzil elanları. Ən yeni elanlar.",
            ];
        } elseif (isset($params['announcementType']) && $params['announcementType'] == 2 && isset($params['client_types_for_rent']) && $params['client_types_for_rent'] == 5 && count($params) == 2) {
            $result = [
                'title' => 'Gənc ailələr üçün sərfəli mənzillər',
                'description' => 'Gənc ailələr üçün sərfəli kirayə evlər. Ən yeni kirayə ev elanları. Bakı və rayonlarda sərfəli şərtlərlə ailələr üçün mənzillər.',
            ];
        } elseif (isset($params['announcementType']) && $params['announcementType'] == 2 && isset($params['city']) && $params['city'] == 1 && count($params) == 2) {
            $result = [
                'title' => "{$cityNameWithSuffix} kirayə evlər",
                'description' => "{$cityNameWithSuffix} kirayə evlər. {$cityNameWithSuffix} ucuz kirayə evlər. {$cityNameWithSuffix} kirayə ev qiymətləri. {$cityNameWithSuffix} ən yeni kirayə ev elanları myhome.az-da!",
            ];
        }

        elseif (isset($params['metro']) && count($params) == 1) {

            $metroStations = MetroStation::whereIn('id', (array) $params['metro'])->pluck('name')->toArray();

            $metroStationsNames = implode(', ', $metroStations);

            $result = [
                'title' => "{$metroStationsNames} metro stansiyaları yaxınlığında mənzil və ev elanları 2024",
                'description' => "{$metroStationsNames} metro stansiyaları yaxınlığında satılan və kirayə verilən mənzillər. Ən yeni daşınmaz əmlak elanları myhome.az-da!",
            ];

        }
        elseif (isset($params['metro']) && isset($params['announcementType'])) {
            $metroStations = MetroStation::whereIn('id', (array) $params['metro'])->pluck('name')->toArray();
            $metroStationsNames = implode(', ', $metroStations);

            // Announcement type 1: Satış
            if ($params['announcementType'] == 1) {
                $result = [
                    'title' => "{$metroStationsNames} metro stansiyaları yaxınlığında satışda olan mənzillər 2024",
                    'description' => "{$metroStationsNames} metro stansiyaları yaxınlığında satılan mənzillər. Ən yeni daşınmaz əmlak satış elanları myhome.az-da!",
                ];
            }
            // Announcement type 2: Kirayə
            elseif ($params['announcementType'] == 2) {
                $result = [
                    'title' => "{$metroStationsNames} metro stansiyaları yaxınlığında kirayə mənzillər 2024",
                    'description' => "{$metroStationsNames} metro stansiyaları yaxınlığında kirayə verilən mənzillər. Ən yeni kirayə daşınmaz əmlak elanları myhome.az-da!",
                ];
            }
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
