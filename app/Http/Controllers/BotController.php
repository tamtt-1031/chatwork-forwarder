<?php

namespace App\Http\Controllers;

use Exception;
use Auth;
use App\Models\Bot;
use App\Repositories\Interfaces\BotRepositoryInterface as BotRepository;
use App\Http\Requests\BotCreateRequest;

class BotController extends Controller
{
    private $botRepository;

    public function __construct(BotRepository $botRepository)
    {
        $this->botRepository = $botRepository;
    }

    public function index()
    {
        $bots = $this->botRepository->getAllByUser();

        return view('bots.index', compact('bots'));
    }

    public function destroy($id)
    {
        try {
            $this->botRepository->delete($id);

            return redirect('/bots')->with('messageSuccess', __('message.bot.notification.delete.success'));
        } catch (Exception $exception) {
            return redirect()->back()->with('messageFail', __('message.bot.notification.delete.fail'));
        }
    }

    public function create()
    {
        return view('bots.create');
    }

    public function store(BotCreateRequest $request)
    {
        $data = $request->except('_token');
        $data['user_id'] = Auth::id();

        try {
            $bot = $this->botRepository->create($data);
            return redirect()->route('bots.edit', $bot)
                             ->with('messageSuccess', 'This bot successfully created');
        } catch (QueryException $exception) {
            return redirect()->back()->with('messageFail', 'Create failed. Something went wrong')->withInput();
        }
    }

    public function edit(Bot $bot)
    {
        return view('bots.edit');
    }
}
