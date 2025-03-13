<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function listUsers()
    {
        $bidders = User::where('role', 'bidder')->get();
        return view('chat.users', compact('bidders'));
    }
    public function showChat($bidderId)
    {
        $bidder = User::find($bidderId);
        $messages = Chat::where(function ($query) use ($bidderId) {
            $query->where('sender_id', Auth::user()->id)
                ->where('receiver_id', $bidderId);
        })
            ->orWhere(function ($query) use ($bidderId) {
                $query->where('sender_id', $bidderId)
                    ->where('receiver_id', Auth::user()->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return view('chat.index', compact('messages', 'bidder', 'bidderId'));
    }
    public function showChatForBidder()
    {
        $adminId = User::where('role', 'admin')->first()->id; 
        $messages = Chat::where(function ($query) use ($adminId) {
            $query->where('sender_id', Auth::user()->id)->where('receiver_id', $adminId);
        })
            ->orWhere(function ($query) use ($adminId) {
                $query->where('sender_id', $adminId)->where('receiver_id', Auth::user()->id);
            })
            ->orderBy('created_at', 'asc') 
            ->get();

        return view('chat.index', compact('messages', 'adminId')); 
    }
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required',
            'receiver_id' => 'required',
        ]);
        
        $chat = new Chat();
        $chat->sender_id = Auth::user()->id;
        $chat->receiver_id = $request->receiver_id;
        $chat->message = $request->message;
        $chat->save();

        event(new MessageSent($chat));

        return response()->json([
            'status' => 'success'
        ]);
    }
}
