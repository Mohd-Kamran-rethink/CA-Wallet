<?php

namespace App\Http\Controllers;

use App\Client;
use App\Deposit;
use App\DepositHistory;
use App\NumberRequest;
use App\Transaction;
use App\TransactionHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;
use Transactions;

class ClientController extends Controller
{
    public function list(Request $req)
    {
        $search = $req->query('saerch-input');
        $startDate = $req->query('from_date');
        $endDate = $req->query('to_date');
        $startDate = $req->query('from_date');
        $endDate = $req->query('to_date');

        if (!$startDate) {
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $startDate . ' 00:00:00');
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $endDate . ' 23:59:59');
        }
        $currentUser = session('user');
        $requestNumber = NumberRequest::where('agent_id', $currentUser->id)->first();
        if ($currentUser->role == "agent") {
            $clients = Client::select('clients.id', 'clients.number')
                // ->lefjoin('transactions', 'clients.id', '=', 'transactions.client_id')
                // ->groupBy('clients.id', 'clients.number')
                // ->where('transactions.type', '=', 'Deposit')
                // ->where('transactions.status', '=', 'Approve')
                ->where('agent_id', '=', $currentUser->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->when($search, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->Where('clients.number', '=', $search);
                    });
                })
                // ->selectRaw('SUM(transactions.amount) as total_amount')
                ->paginate();
        } elseif ($currentUser->role == "manager") {
            $clients = Client::select('clients.id', 'clients.number')->whereBetween('created_at', [$startDate, $endDate])
                ->when($search, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->Where('clients.number', '=', $search);
                    });
                })->paginate();
        }
        $startDate = $startDate->toDateString();
        $endDate = $endDate->toDateString();
        return view('Admin.Clients.list', compact('requestNumber', 'search', 'clients', 'startDate', 'endDate'));
    }
    public function addView(Request $req)
    {
        $client = null;
        $id = $req->query('id');
        $client = Client::find($id);
        return view('Admin.Clients.add', compact('client'));
    }
    public function add(Request $req)
    {
        $req->validate([
            'name' => 'required',
            'ca_id' => 'required|unique:clients,ca_id',
            'number' => 'required|unique:clients,number',
        ]);
        $Agent = session('user');
        $client = new Client();
        $client->name = $req->name;
        $client->number = $req->number;
        $client->ca_id = $req->ca_id;
        $client->agent_id = $Agent->id;
        $result = $client->save();
        if ($result) {
            return redirect('/clients')->with(['msg-success' => 'Client has been added.']);
        } else {
            return redirect('/clients')->with(['msg-error' => 'Something went wrong could not add client.']);
        }
    }
    public function delete(Request $req)
    {
        $client = Client::find($req->deleteId);
        $client->isDeleted = "Yes";
        $result = $client->update();
        if ($result) {
            return redirect('/clients')->with(['msg-success' => 'Client has been deleted.']);
        } else {
            return redirect('/clients')->with(['msg-error' => 'Something went wrong could not delete client.']);
        }
    }
    public function edit(Request $req)
    {
        $req->validate([
            'name' => 'required',
            'ca_id' => 'required',
            'number' => 'required',
        ]);
        $Agent = session('user');
        $client = Client::find($req->userId);
        $client->name = $req->name;
        $client->number = $req->number;
        $client->ca_id = $req->ca_id;
        $client->agent_id = $Agent->id;
        $result = $client->update();
        if ($result) {
            return redirect('/clients')->with(['msg-success' => 'Client has been updated.']);
        } else {
            return redirect('/clients')->with(['msg-error' => 'Something went wrong could not update client.']);
        }
    }
    public function redeposit(Request $req)
    {
        $currentUser = session('user');
        $deposit = Deposit::where('client_id', '=', $req->depositId)->where('agent_id', '=', $currentUser->id)->first();
        if ($deposit) {
            $deposit->deposit_amount = $deposit->deposit_amount + $req->amount;
            $deposit->type = 'redeposit';
            $deposit->update();
        } else {
            $deposit = new Deposit();
            $deposit->deposit_amount = $req->amount;
            $deposit->type = 'Deposit';
            $deposit->agent_id = $currentUser->id;
            $deposit->client_id = $req->depositId;
            $deposit->save();
        }
        $depositHistory = new DepositHistory();
        $depositHistory->deposit_id = $deposit->id;
        $depositHistory->amount = $req->amount;
        $depositHistory->type = "Redeposit";
        $result = $depositHistory->save();
        if ($result) {
            return redirect('/clients')->with(['msg-success' => 'Amount has been redeposited.']);
        } else {
            return redirect('/clients')->with(['msg-error' => 'Something went wrong could not redeposit amount.']);
        }
    }

    public function depositHistory($id)
    {
        $agentId = session('user')->id;
        $deposit = Deposit::where('client_id', '=', $id)->where('agent_id', '=', $agentId)->first();
        if ($deposit) {
            $depositHistory = DepositHistory::where('deposit_id', '=', $deposit->id)->orderBy('id', 'desc')->paginate();
            return view('Admin.Clients.depositList', compact('depositHistory'));
        } else {
            return redirect()->back()->with(['msg-error' => 'Sorry you dont have data for this client']);
        }
    }

    public function AcceptrequestNumber(Request $req)
    {
        $msg = '';
        $request = NumberRequest::where('agent_id', '=', $req->agent_id)->first();
        $request->approved = $req->status;
        $request->update();
        if ($req->status == "Yes") {
            $msg = "Request has been approved successfully";
        } else {
            $msg = "Request has been reject";
        }

        return redirect('/agents/number/requests')->with(['msg-success' => $msg]);
    }
    public function numberRequest(Request $req)
    {
        $request = new NumberRequest();
        $request->agent_id = session('user')->id;
        $request->save();
        return redirect('/clients')->with(['msg-success' => "Request has been sent"]);
    }
    public function transactionHistoy(Request $req)
    {
        $agentId = session('user')->id;
        $id = $req->query('id');
        $client = Client::find($id);
        if ($client->agent_id != $agentId) {
            return redirect()->back()->with(['msg-error' => 'Invalid id use only your clients id']);
        }
        $startDate = $req->query('from_date') ?? null;
        $endDate = $req->query('to_date') ?? null;
        $type = $req->query('type') ?? 'null';
        if (!$startDate) {
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } else {
            $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
        }

        $activites = Transaction::where('client_id', '=', $id)
            ->when($type !== 'null', function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->whereDate('created_at', '>=', date('Y-m-d', strtotime($startDate)))
            ->whereDate('created_at', '<=', date('Y-m-d', strtotime($endDate)))
            ->get();
        $startDate = $startDate->toDateString();
        $endDate = $endDate->toDateString();
        return view('Admin.Clients.history', compact('activites', 'id', 'startDate', 'endDate'));
    }

    function checkUtr(Request $req)
    {
        $tranasction = null;
        $status = null;
        $warn = null;
        $utr = $req->utr;

        $tranasction = Transaction::where('utr_no', '=', $utr)->where('type', '=', 'Deposit')->first();
        if ($utr) {

            if ($tranasction) {
                $status = "Deposit has been done";
                $warn = 'success';
            } else {
                $warn = 'danger';
                $status = "Couldn't find UTR";
            }
        }
        return view('Admin.Clients.checkUtr', compact('utr', 'status', 'warn'));
    }
}
