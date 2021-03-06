<?php

namespace App\Repositories;

use App\Models\Credit;
use Storage;
use DB;
use Illuminate\Http\UploadedFile;

Class CreditRepository
{

    const CREDIT_COL = [
        'name', 'brand_id', 'rate', 'pay', 'contribution', 'period', 'begin_at', 'end_at', 'disclaimer', 'status', 'banner',
    ];

    public function save(Credit $credit, $data = []) :array
    {
        try {
            $result = DB::transaction(function () use ($data, $credit) {
                if (isset($data['banner']) && $data['banner'] instanceof UploadedFile) {
                    $data['banner'] = $this->saveBanner($data['banner']);
                } else {
                    unset($data['banner']);
                }

                $this->saveMain(
                    $credit,
                    array_filter($data, function ($key) {
                        if (\array_key_exists($key, array_flip(self::CREDIT_COL))) {
                            return true;
                        }
                    }, ARRAY_FILTER_USE_KEY)
                );
                if (isset($data['marks'])) {
                    $this->saveMarks($credit, $data['marks']);
                }
            });
            return [
                'status' => 1
            ];
        } catch(\Exception $e) {
            return [
                'status' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function saveMain(Credit $credit, $data = [])
    {
        $credit->fill($data)->save();
    }

    private function saveBanner($file) :string
    {
        $bannerName = 'banner_'.date('dmyhms').'.'.$file->getClientOriginalExtension();
        $path = '/public/credit/';
        $urlPath = '/credit/';
        $finalName = $urlPath.'/'.$file->move(Storage::path($path), $bannerName)->getFilename();
        return $finalName;
    }

    private function saveMarks(Credit $credit, $data = [])
    {
        $credit->marks()->sync($data);
    }

    public function getCreditArray(Credit $credit) :array
    {
        $data = $credit->toArray();
        $data['marks'] = $credit->marks->pluck('id');
        $data['banner'] = asset('storage/'.$credit->banner) . '?'.date('dmYhms');

        return $data;
    }

    public function getCreditsByMarkId($mark_id) 
    {
        $credits = Credit::join('credit_marks', 'credit_marks.credit_id', '=', 'credits.id')
            ->where('credit_marks.mark_id', $mark_id)
            ->get();
        foreach ($credits as $key => $item) {
            $item->banner = asset('storage/'.$item->banner) . '?'.date('dmYh');
        }
        return $credits;
    }
}
