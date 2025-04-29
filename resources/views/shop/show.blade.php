@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
        <div class="flex flex-col md:flex-row">
            <!-- Product Image -->
            <div class="md:w-1/2">
                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-auto object-cover rounded-lg">
                
                <!-- Color Options -->
                <div class="mt-4 flex space-x-2">
                    @foreach ($product->