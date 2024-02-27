<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Filterable;
use App\Models\Interfaces\CommentInterface;
use App\Models\WSMRedemptionComment;

class WSMRedemptionCar extends Model implements CommentInterface
{
    use HasFactory, Filterable;

    protected $guarded = [];

    protected $table = 'wsm_redemption_cars';

    public function writeComment(array $data)
    {
        return WSMRedemptionComment::create($data);
    }

    public function lastAuthor()
    {
        $author = $this->author;

        if($this->calculations->last())
            $author = $this->calculations->last()->author;

        if($this->offers->last())
            $author = $this->offers->last()->author;

        if($this->purchases->last())
            $author = $this->purchases->last()->author;

        if($this->redemption_status_id == 3 || $this->redemption_status_id == 2)
            $author = $this->final_author->author;

        return $author->cut_name;
    }

    public function isWaiting()
    {
        $res = !$this->calculations->last() && !$this->offers->last() && !$this->purchases->last();
        return $res;
    }

    public function control_point()
    {
        $arr = [];
        if($this->redemption_status_id == 3)
            $arr = ['title' => 'Упущена', 'color' => 'red', 'text' => ''];

        if($this->redemption_status_id == 2)
            $arr = ['title' => 'На складе', 'color' => 'green', 'text' => ''];

        if($this->redemption_status_id == 1)
            if($this->isWaiting())
                $arr = ['title' => 'Ожидает', 'color' => 'yellow', 'text' => ''];
            elseif(!$this->isWaiting())
                $arr = ['title' => 'В работе', 'color' => 'yellow', 'text' => ''];
            // if($this->worksheet->isClosing() && $this->isWaiting())
            //     $arr = ['title' => 'Ожидает', 'color' => 'red', 'text' => 'Рабочий лист завершен, а оценка «Ожидает»'];

            // if($this->worksheet->isClosing() && !$this->isWaiting())
            //     $arr = ['title' => 'В работе', 'color' => 'red', 'text' => 'Рабочий лист завершен, а оценка «Ожидает»'];
            //elseif($this->last_purchase->price)
                //$arr = ['title' => 'В работе', 'color' => 'red', 'text' => 'Реквизит «Согласовано» заполнен, но команда «Переместить на склад» не применялась'];

            // elseif($this->isWaiting())
            //     $arr = ['title' => 'Ожидает', 'color' => 'yellow', 'text' => ''];

            else
                $arr = ['title' => 'В работе', 'color' => 'yellow', 'text' => ''];
        return (object) $arr;
    }



    public function final_author()
    {
        return $this->hasOne(\App\Models\WSMRedemptionFinalizer::class, 'wsm_redemption_car_id', 'id')->withDefault();
    }

    public function worksheet()
    {
        return $this->hasOne(\App\Models\Worksheet::class, 'id', 'worksheet_id');
    }

    public function client_car()
    {
        return $this->hasOne(\App\Models\ClientCar::class, 'id', 'client_car_id')->withDefault();
    }

    public function type()
    {
        return $this->hasOne(\App\Models\RedemptionType::class, 'id', 'redemption_type_id')->withDefault();
    }

    public function car_sale_sign()
    {
        return $this->hasOne(\App\Models\CarSaleSign::class, 'id', 'car_sale_sign_id')->withDefault();
    }

    public function author()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'author_id')->withDefault();
    }

    public function client()
    {
        return $this->hasOne(\App\Models\Client::class, 'id', 'client_id')->withDefault();
    }

    public function last_calculation()
    {
        return $this->hasOne(\App\Models\WSMRedemptionCalculation::class, 'wsm_redemption_car_id', 'id')->orderBy('id', 'DESC')->withDefault();
    }

    public function last_purchase()
    {
        return $this->hasOne(\App\Models\WSMRedemptionPurchase::class, 'wsm_redemption_car_id', 'id')->orderBy('id', 'DESC')->withDefault();
    }

    public function calculations()
    {
        return $this->hasMany(\App\Models\WSMRedemptionCalculation::class, 'wsm_redemption_car_id', 'id')->orderBy('id', 'DESC');
    }

    public function purchases()
    {
        return $this->hasMany(\App\Models\WSMRedemptionPurchase::class, 'wsm_redemption_car_id', 'id')->orderBy('id', 'DESC');
    }

    public function last_offer()
    {
        return $this->hasOne(\App\Models\WSMRedemptionOffer::class, 'wsm_redemption_car_id', 'id')->orderBy('id', 'DESC')->withDefault();
    }

    public function offers()
    {
        return $this->hasMany(\App\Models\WSMRedemptionOffer::class, 'wsm_redemption_car_id', 'id')->orderBy('id', 'DESC');
    }

    public function links()
    {
        return $this->hasMany(\App\Models\WSMRedemptionLink::class, 'wsm_redemption_car_id', 'id');
    }

    public function status()
    {
        return $this->hasOne(\App\Models\RedemptionStatus::class, 'id', 'redemption_status_id')->withDefault();
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\WSMRedemptionComment::class, 'redemption_car_id', 'id')->orderBy('id', 'DESC');
    }

    public function last_comment()
    {
        return $this->hasOne(\App\Models\WSMRedemptionComment::class, 'redemption_car_id', 'id')->orderBy('id', 'DESC');
    }

    public function apprailsal()
    {
        return $this->hasOne(\App\Models\WSMRedemptionAppraisal::class, 'redemption_id', 'id');
    }
}
