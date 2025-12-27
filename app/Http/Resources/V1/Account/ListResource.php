<?php

namespace App\Http\Resources\V1\Account;

use App\Models\V1\Account;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
  public function toArray($request)
  {
    $status = ($this->status == Account::STATUS_ACTIVE)?Account::STATUS_ACTIVE:'pending';
    return [
      "id"            => $this->id,
      "nickname" => $this->nickname,
      "client_id"     => $this->client_id,
      "account_name"  => $this->account_name,
      "status"        => $status,
      "token_expiry"  => $this->formatExpiry(),
      "last_login_at" => $this->formatDate($this->last_login_at),
      "last_error_code" => $this->last_error_code,
    ];
  }

  private function formatExpiry()
  {
    if (!$this->token_expiry) {
      return '<span class="badge bg-label-secondary">—</span>';
    }

    $expiry = Carbon::parse($this->token_expiry);
    $now = Carbon::now();

    if ($expiry->isPast()) {
      return '<span class="badge bg-label-danger">Expired</span>';
    }

    $remaining = $now->diffForHumans($expiry, true);

    return "<span class='badge bg-label-info'>{$remaining} left</span>";
  }

  private function formatDate($date)
  {
    return $date ? Carbon::parse($date)->format("d/m/Y H:i") : "-";
  }
}
