<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use simplehtmldom\HtmlWeb;

class ParseJobListings extends Command
{
    protected $signature = 'parse:jobs';
    protected $description = 'Parse job listings from arbeitsagentur.de';

    public function handle()
    {
        $url = 'https://www.arbeitsagentur.de/jobsuche/suche?angebotsart=4&was=kochen&wo=Berlin&umkreis=25';

        $html = new HtmlWeb();
        $dom = $html->load($url);

        if (!$dom) {
            $this->error("Failed to load the page.");
            return;
        }

        foreach ($dom->find('.jobsuche-resultlist-item') as $job) {
            $title = trim($job->find('.jobsuche-resultlist-item-header h3', 0)->plaintext ?? 'N/A');
            $company = trim($job->find('.jobsuche-resultlist-item-subtitle', 0)->plaintext ?? 'N/A');
            $location = trim($job->find('.jobsuche-resultlist-item-location', 0)->plaintext ?? 'N/A');
            $link = $job->find('a', 0)->href ?? '#';

            $this->info("Job: $title");
            $this->info("Company: $company");
            $this->info("Location: $location");
            $this->info("Link: https://www.arbeitsagentur.de$link");
            $this->info("-------------------------------");
        }
    }
}
