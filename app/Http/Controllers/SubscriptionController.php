<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    public function expired(): Response
    {
        return Inertia::render('Subscription/Expired');
    }

    public function checkout(): Response
    {
        return Inertia::render('Subscription/Checkout');
    }
} 