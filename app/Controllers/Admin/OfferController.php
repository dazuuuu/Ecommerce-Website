<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\View;
use App\Models\Product;

class OfferController extends BaseAdminController
{
    public function index(): void
    {
        View::render('admin.offers.index', [
            'pageTitle' => 'Offers',
            'activeNav' => 'offers',
            'onOffer' => Product::onOffer(),
            'notOnOffer' => Product::notOnOffer(),
        ]);
    }

    public function update(string $id): void
    {
        if (csrfVerify(Request::post('csrf_token'))) {
            $price = (float) Request::post('price', 0);
            $original = Request::post('original_price', '') !== '' ? (float) Request::post('original_price') : null;
            Product::updateOfferPricing((int) $id, $price, $original);
            flashSuccess('Offer updated.');
        }
        redirect('/admin/offers');
    }

    public function remove(string $id): void
    {
        if (csrfVerify(Request::post('csrf_token'))) {
            Product::removeFromOffers((int) $id);
            flashSuccess('Product removed from offers.');
        }
        redirect('/admin/offers');
    }

    public function add(string $id): void
    {
        if (csrfVerify(Request::post('csrf_token'))) {
            Product::addToOffers((int) $id);
            flashSuccess('Product added to offers — set its discounted price below.');
        }
        redirect('/admin/offers');
    }
}
