<?php

namespace App\Http\Controllers\Employee\Transaction;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Partner\HumanResource\Employee;
use App\Models\Product\Product;
use App\Models\Transaction\OrderPayment;
use App\Models\Partner\Products\PartnerProduct;
use App\Models\Partner\Products\PartnerProductParentOption;
use App\Models\Partner\Products\PartnerProductOption;
use App\Models\Transaction\BookingOrder;
use App\Models\Transaction\OrderDetail;
use App\Models\Transaction\OrderDetailOption;
use App\Models\Product\Specification;
use App\Models\Admin\Product\Category;
use App\Models\User;
use App\Models\Store\Table;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Events\OrderCreated;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class KitchenTransactionController extends Controller {}
