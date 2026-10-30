@extends('errors.layout')

@section('code', '403')
@section('title', 'Akses Ditolak')
@section('message', $exception->getMessage() ?: 'Anda tidak memiliki akses ke halaman ini.')
