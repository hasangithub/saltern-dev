<?php 
namespace Database\Seeders;
use App\Models\Yahai;
use App\Models\Side;
use Illuminate\Database\Seeder;

class YahaiSeeder extends Seeder
{
    public function run()
    {
        $yahais = [
            'west' => [
                'Ariyakuda West',
                'Keeri Mundel',
                'Cinna Keeri mundel',
                'Pattiyadi Kuda',
                'Palayadi Kuda',
                'Passaradi Mundel',
                'Mayilvelly',
                'Mullipuram',
                'Palliyadi Mundel',
            ],
            'east' => [
                'Ariyakuda East',
                'Puthoor',
                'Sengalpity',
                'Ponnadanja Kuda',
                'Mariyariyahai',
                'Uppadanja Kuda',
                'Palanthivu',
                'Palanthivu Tharawa',
            ],
        ];

        foreach ($yahais as $sideName => $yahaiList) {
            $side = Side::where('name', $sideName)->first();

            foreach ($yahaiList as $yahaiName) {
                Yahai::create([
                    'name' => $yahaiName,
                    'side_id' => $side->id,
                ]);
            }
        }
    }
}
