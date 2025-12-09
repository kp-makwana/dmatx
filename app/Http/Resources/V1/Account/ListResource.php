<?php

namespace App\Http\Resources\V1\Account;

use Carbon\Carbon;
use Illuminate\Http\Request;
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
    return [
      "id"            => $this->id,
      "nickname" => $this->getNickname(),
      "client_display" => $this->getClientDisplay(),
      "client_id"     => $this->client_id,
      "account_name"  => $this->account_name,
      "status"        => $this->status,
      "status_label"  => ucfirst($this->status),
//      "is_active"     => $this->is_active,
      "token_expiry"  => $this->formatExpiry(),
      "last_login_at" => $this->formatDate($this->last_login_at),
    ];
  }

  private function getClientDisplay()
  {
    $avatarText = strtoupper(substr($this->nickname ?: $this->client_id, 0, 2));

    return '
            <div class="d-flex align-items-center">
                <div class="d-flex flex-column">
                    <span class="fw-medium">' . $this->client_id . '</span>
                    <small>' . e($this->account_name ?: "N/A") . '</small>
                </div>
            </div>
        ';
  }

  public function getNickname()
  {
    $avatarText = strtoupper(substr($this->nickname ?: $this->client_id, 0, 2));

    return '
            <div class="d-flex align-items-center">
                <div class="avatar avatar-sm me-4">
                    <span class="avatar-initial rounded-circle bg-label-primary">
                        ' . $avatarText . '
                    </span>
                </div>

                <div class="d-flex flex-column">
                    <span class="fw-medium">' . e($this->nickname ?: $this->client_id) . '</span>
                </div>
            </div>
        ';
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
