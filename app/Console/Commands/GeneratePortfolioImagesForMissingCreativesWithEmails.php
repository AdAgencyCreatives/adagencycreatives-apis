<?php

namespace App\Console\Commands;

use App\Jobs\ProcessPortfolioVisuals;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Console\Command;

class GeneratePortfolioImagesForMissingCreativesWithEmails extends Command
{
    protected $signature = 'generate:missing-portfolios-with-emails';
    protected $description = 'It generates portfolio previews for missing creatives';

    public function handle()
    {
        $fullNames = [
            'Joyce Pedretti',
            'Shawn Diaz',
            'Jennifer Guzman',
            'Andrew Weissman',
            'Christopher Barker',
            'Tim Min',
            'Jared Rubin',
            'Heather Wagner',
            'Samiksha Puri',
            'Jessica Norris',
            'Jeremy Filgate',
            'Will Houser',
            'Daran Brossard',
            'Marisol Medina',
            'Mariana Weinzimer',
            'Daniel Ringelberg',
            'Sonya Yan',
            'Xiaohe(Hailee) Zhang',
            'Belen Stanicio',
            'Nicholas Mueller',
            'Mike Merritt',
            'Pete Pallett',
            'Anthony Brooks',
            'Destynie Yi',
            'Joey Jones',
            'Tejal Shinde',
            'James Merendino',
            'Hailey Joseph',
            'Linda Lam',
            'George Wu',
            'Julian Esposito',
            'Paul Butterfield',
            'Brittany Gardner',
            'Karas Lamb',
            'Beau Annable',
            'Michelle Lukezic',
            'Carl Von der Lancken',
            'John Evans',
            'Justin Lindberg',
            'Rory O’Connor',
            'Katrina Iorio',
            'Steven Stark',
            'Chris Hall',
            'Jana Landers',
            'Hannah Levin',
            'Scott Bandler',
            'Laney Davis',
            'Terry Orsland',
            'Victoria Oshodin',
            'Amina Shreve',
            'Charlotte Andrews',
            'Jamie Shin',
            'Amanda Wennberg',
            'Silvia Colsher',
            'Annelie Rode',
            'Candie Velasco',
            'Joshua Bull',
            'Hector Jamesson',
            'Marvin Figueroa',
            'Aril Worrell',
            'Taylor Murdock',
            'Renan McFarland',
            'Robert Aquadro',
            'Matt Broccoli',
            'Exilia Han',
            'Cassidy Marcum',
            'Emily Ludin',
            'Carlos Riveroll',
            'Aron Shand',
            'Javier Vela',
            'Adam Peterson',
            'Matthew Perry',
            'Cameron Rivas',
            'Micah Mackert',
            'Ryan Shivers',
            'Brooke Weber',
            'Chris Bettin',
            'Jeff Sloan',
            'Luzdivina Ruiz',
            'Caleigh Ripp',
            'Adam Villarreal',
            'Danielle Ancrile',
            'Joe Moon',
            'Brant Herzer',
            'Monica Hoang',
        ];

        $count = 1;
        foreach($fullNames as $creative) {
            $name = explode(' ', $creative);
            $user = User::where('first_name', $name[0])->orWhere('last_name', $name[1])->first();
            // dump(sprintf("%d %s %s %s", $count,$user->first_name, $user->last_name, $user->email));
            $count++;
            // continue;
            $portfolio_website = $user->portfolio_website_link()->first();
            if ($portfolio_website) {
                Attachment::where('user_id', $user->id)->where('resource_type', 'website_preview')->delete();

                dump($user->id, $portfolio_website->url);
                ProcessPortfolioVisuals::dispatch($user->id, $portfolio_website->url);


            }
        }
    }
}