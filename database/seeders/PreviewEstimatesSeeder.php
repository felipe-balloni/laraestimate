<?php

namespace Database\Seeders;

use App\Models\Estimate;
use App\Models\Item;
use App\Models\Section;
use Illuminate\Database\Seeder;

class PreviewEstimatesSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        Estimate::factory()->create([
            'name' => 'Site Development Estimate'
        ])->each(function ($estimate) {
            $textSectionData = Section::factory()->make([
                'text' => "<b>Introduction</b><br>This is an example estimate so you can view some of the resources available. You can add as many text sections like this <b>Introduction</b> as you want.<br>All sections accepts <i>Rich-Text</i> content."
            ])->toArray();
            unset($textSectionData['presentable_text']);
            $estimate->sections()->create($textSectionData);

            $pricesSectionData = Section::factory()->make([
                'text' => '<b>Price Section</b><br>Here in the price section, it is possible to add mandatory items or not.',
                'type' => 'prices'
            ])->toArray();
            unset($pricesSectionData['presentable_text']);

            $pricesSection = $estimate->sections()->create($pricesSectionData);

            $itemData = Item::factory()->make([
                'description' => 'Initial Setup, Research and Preparation',
                'duration' => '5 days',
                'obligatory' => true
            ])->toArray();
            $pricesSection->items()->create($itemData);

            $itemData = Item::factory()->make([
                'description' => 'Home Page',
                'duration' => '3 days',
                'obligatory' => true
            ])->toArray();
            $pricesSection->items()->create($itemData);

            $itemData = Item::factory()->make([
                'description' => 'Contact Page',
                'duration' => '2 days',
                'obligatory' => false
            ])->toArray();
            $pricesSection->items()->create($itemData);

            $itemData = Item::factory()->make([
                'description' => 'About Page',
                'duration' => '1 day',
                'obligatory' => false
            ])->toArray();
            $pricesSection->items()->create($itemData);

            $itemData = Item::factory()->make([
                'description' => 'Blog',
                'duration' => '5 days',
                'obligatory' => false
            ])->toArray();
            $pricesSection->items()->create($itemData);

            $textSectionData = Section::factory()->make([
                'text' => "<b>Another Text Section</b><br>You can also put the total budget price wherever you want, as well as the selected total price, for example:
                <br><br>
                This is the total budget price: <b>*TOTAL_PRICE*</b>
                <br><br>
                And this is the total price selected: <b>*TOTAL_SELECTED_PRICE*</b>
                <br><br>
                Try to selecet/unselect the items below to see the selected price changing
                "
            ])->toArray();
            unset($textSectionData['presentable_text']);
            $estimate->sections()->create($textSectionData);

            $pricesSectionData = Section::factory()->make([
                'text' => '<b>Another Price Section</b><br>You can add as many price sections as you like. Let\'s see the selected price (*TOTAL_SELECTED_PRICE*) changing',
                'type' => 'prices'
            ])->toArray();
            unset($pricesSectionData['presentable_text']);

            $pricesSection = $estimate->sections()->create($pricesSectionData);

            $itemData = Item::factory()->make([
                'description' => 'Add E-commerce',
                'duration' => '13 days',
                'obligatory' => false
            ])->toArray();
            $pricesSection->items()->create($itemData);

            $itemData = Item::factory()->make([
                'description' => 'Create the Logotype',
                'duration' => '20 days',
                'obligatory' => false
            ])->toArray();
            $pricesSection->items()->create($itemData);
        });
    }
}
