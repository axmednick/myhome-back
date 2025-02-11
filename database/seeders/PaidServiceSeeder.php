<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaidService;
use App\Models\PaidServiceOption;

class PaidServiceSeeder extends Seeder
{
    public function run()
    {
        // Ödənişli xidmətlər və onların variantları
        $services = [
            [
                'name' => 'Elanı İrəli Çək',
                'type' => 'forward',
                'description' => 'Elan bütün və öz kateqoriyasındakı elanlarının axtarış nəticələrinin içində birinci yerə qalxacaq.',
                'options' => [
                    ['duration' => 1, 'duration_type' => 'times', 'price' => 1.00],
                    ['duration' => 3, 'duration_type' => 'times', 'price' => 2.00],
                    ['duration' => 5, 'duration_type' => 'times', 'price' => 3.00],
                    ['duration' => 10, 'duration_type' => 'times', 'price' => 5.00],
                ],
            ],
            [
                'name' => 'VIP Et',
                'type' => 'vip',
                'description' => 'Elan VIP bölməsində və öz kateqoriyasındakı elanlarının axtarış nəticələrinin içində təsadüfi qaydada göstəriləcək.',
                'options' => [
                    ['duration' => 1, 'duration_type' => 'day', 'price' => 3.00],
                    ['duration' => 5, 'duration_type' => 'day', 'price' => 7.00],
                    ['duration' => 15, 'duration_type' => 'day', 'price' => 15.00],
                    ['duration' => 30, 'duration_type' => 'day', 'price' => 20.00],
                ],
            ],
            [
                'name' => 'Premium Et',
                'type' => 'premium',
                'description' => 'Elan əsas səhifədə göstəriləcək və xidmətin aktivlik müddətinin sonunadək orada qalacaq.',
                'options' => [
                    ['duration' => 1, 'duration_type' => 'day', 'price' => 5.00],
                    ['duration' => 5, 'duration_type' => 'day', 'price' => 15.00],
                    ['duration' => 15, 'duration_type' => 'day', 'price' => 25.00],
                    ['duration' => 30, 'duration_type' => 'day', 'price' => 40.00],
                ],
            ],
        ];

        foreach ($services as $serviceData) {
            // Xidməti yaradın və bazaya əlavə edin
            $service = PaidService::updateOrCreate(
                ['type' => $serviceData['type']],
                [
                    'name' => $serviceData['name'],
                    'description' => $serviceData['description'],
                ]
            );

            foreach ($serviceData['options'] as $option) {
                // Hər bir xidmət üçün seçimləri yaradın
                PaidServiceOption::updateOrCreate(
                    [
                        'service_id' => $service->id,
                        'duration' => $option['duration'],
                        'duration_type' => $option['duration_type'],
                    ],
                    [
                        'price' => $option['price'],
                    ]
                );
            }
        }
    }
}
