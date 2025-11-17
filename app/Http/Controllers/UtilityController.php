<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Film;

class UtilityController extends Controller
{
    public function debugOrders()
    {
        try {
            $orders = Order::with(['schedule.film', 'schedule.studio', 'orderItems.seat', 'user'])->get();
            return response()->json([
                'success' => true,
                'total' => $orders->count(),
                'orders' => $orders->map(function($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'order_type' => $order->order_type,
                        'payment_status' => $order->payment_status,
                        'total_amount' => $order->total_amount,
                        'user_email' => $order->user->email ?? 'N/A',
                        'film' => $order->schedule->film->title ?? 'N/A',
                        'seats_count' => $order->orderItems->count(),
                        'created_at' => $order->created_at,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function cleanupPendingOrders()
    {
        try {
            // Delete pending orders older than 1 hour
            $deleted = Order::where('payment_status', 'pending')
                ->where('created_at', '<', now()->subHour())
                ->delete();
            
            return response()->json([
                'success' => true,
                'message' => "Deleted {$deleted} old pending orders"
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function fixFilmsProduction()
    {
        try {
            // Delete old films
            $oldFilms = ['Oppenheimer', 'The Batman', 'Spider-Man', 'Top Gun', 'Avengers'];
            foreach ($oldFilms as $title) {
                Film::where('title', 'LIKE', "%{$title}%")->delete();
            }
            
            // Ensure new films exist with correct data
            $films = [
                ['title' => 'Deadpool & Wolverine', 'poster' => 'https://image.tmdb.org/t/p/w500/8cdWjvZQUExUUTzyp4t6EDMubfO.jpg', 'trailer' => 'https://www.youtube.com/watch?v=73_1biulkYk', 'genre' => 'Action, Comedy', 'duration' => 128, 'status' => 'play_now', 'description' => 'Deadpool teams up with Wolverine in an epic adventure across the multiverse.', 'base_price' => 55000],
                ['title' => 'Inside Out 2', 'poster' => 'https://image.tmdb.org/t/p/w500/vpnVM9B6NMmQpWeZvzLvDESb2QY.jpg', 'trailer' => 'https://www.youtube.com/watch?v=LEjhY15eCx0', 'genre' => 'Animation, Family', 'duration' => 96, 'status' => 'play_now', 'description' => 'Riley enters puberty and experiences a whole new set of emotions.', 'base_price' => 45000],
                ['title' => 'Dune: Part Two', 'poster' => 'https://image.tmdb.org/t/p/w500/1pdfLvkbY9ohJlCjQH2CZjjYVvJ.jpg', 'trailer' => 'https://www.youtube.com/watch?v=Way9Dexny3w', 'genre' => 'Sci-Fi, Adventure', 'duration' => 166, 'status' => 'play_now', 'description' => 'Paul Atreides unites with Chani and the Fremen to seek revenge.', 'base_price' => 52000],
                ['title' => 'Wicked', 'poster' => 'https://image.tmdb.org/t/p/w500/c5Tqxeo1UpBvnAc3csUm7j3hlQl.jpg', 'trailer' => 'https://www.youtube.com/watch?v=6COmYeLsz4c', 'genre' => 'Fantasy, Musical', 'duration' => 160, 'status' => 'coming_soon', 'description' => 'The untold story of the Witches of Oz before Dorothy arrived.', 'base_price' => 50000],
                ['title' => 'Moana 2', 'poster' => 'https://image.tmdb.org/t/p/w500/yh64qw9mgXBvlaWDi7Q9tpUBAvH.jpg', 'trailer' => 'https://www.youtube.com/watch?v=hDZ7y8RP5HE', 'genre' => 'Animation, Adventure', 'duration' => 100, 'status' => 'coming_soon', 'description' => 'Moana embarks on a new oceanic adventure with her friends.', 'base_price' => 48000],
                ['title' => 'Gladiator II', 'poster' => 'https://image.tmdb.org/t/p/w500/2cxhvwyEwRlysAmRH4iodkvo0z5.jpg', 'trailer' => 'https://www.youtube.com/watch?v=nkD35yv1RM0', 'genre' => 'Action, Drama', 'duration' => 148, 'status' => 'coming_soon', 'description' => 'The epic saga continues with a new gladiator rising to power.', 'base_price' => 55000]
            ];
            
            foreach ($films as $filmData) {
                Film::updateOrCreate(
                    ['title' => $filmData['title']],
                    $filmData
                );
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Films fixed successfully',
                'films' => Film::select('id', 'title', 'poster')->get()
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
