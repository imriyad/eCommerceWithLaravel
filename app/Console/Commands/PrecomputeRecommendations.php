<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductRecommender;


class PrecomputeRecommendations extends Command
{
    protected $signature = 'recommendations:precompute';
    protected $description = 'Precompute product recommendations';

    protected $recommender;
    
}
