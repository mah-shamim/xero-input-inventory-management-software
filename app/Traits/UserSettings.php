<?php
/**
 * This file is a part of MicroDreamIT
 * (c) Shahanur Sharif <shahanur.sharif@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * or visit https://microdreamit.com
 * Created by Shahanur Sharif.
 * User: sharif
 * Date: 2/7/2018
 * Time: 4:01 PM
 */

namespace App\Traits;

use App\Models\Setting;

trait UserSettings
{
    public function setting()
    {
        return $this->hasOne(Setting::class, 'company_id', 'company_id');
    }
}
