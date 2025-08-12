<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;



class Property extends Model
{

    use HasFactory, SoftDeletes;

    protected $table = 'properties';

    protected $fillable = [
        'title',
        'description',
        'property_type',
        'status',
        'price',
        'area',
        'bedrooms',
        'bathrooms',
        'floors',
        'address',
        'city',
        'district',
        'postal_code',
        'latitude',
        'longitude',
        'year_built',
        'features',
        'images',
        'contact_name',
        'contact_phone',
        'contact_email',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'area' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'features' => 'array',
        'images' => 'array',
        'status' => 'string',
        'property_type' => 'string',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'floors' => 'integer',
        'year_built' => 'integer',
    ];

    public function images()
    {
        return $this->hasMany(PropertyImage::class, 'property_id');
    }
}
