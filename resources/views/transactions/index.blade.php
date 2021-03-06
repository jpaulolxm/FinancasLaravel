@extends('layouts.app')

@section('title')
  {{__('transactions.title')}}
@endsection

@section('title-buttons')
  <a class="btn btn-secondary" href="/accounts">
    <i class="fa fa-arrow-left"></i> {{__('common.back')}}
  </a>
  @if (isset($account))
    <a class="btn btn-primary" title="{{__('common.add')}}" href="/account/{{$account->id}}/transaction/create">
      <i class="fa fa-plus"></i> {{__('common.add')}}
    </a>
  @endif
@endsection

@section('content')
  <?php
  $query = (isset($_GET['description']) ?'description='.$_GET['description'] : '').'&'.((isset($_GET['date_init']) && isset($_GET['date_end'])) ? 'date_init='.$_GET['date_init'].'&date_end='.$_GET['date_end'] : '');
  ?>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <h4>{{__('common.filter')}}</h4>
        {{ Form::open(['url' => (isset($account) ? '/account/'. $account->id : '' ) . '/transactions/', 'method'=>'GET', 'class'=>'form-inline']) }}
          {{ Form::hidden('description', old('description')) }}
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-12">
                {{ Form::label('description', __('common.description')) }}
                {{ Form::text('description', old('description'), ['class'=>'form-control', 'style'=>'width:100%;']) }}
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                {{ Form::label('date_init', __('common.date_init')) }}
                {{ Form::date('date_init', old('date_init'), ['class'=>'form-control', 'style'=>'width:100%;']) }}
              </div>
              <div class="col-md-4">
                {{ Form::label('date_end', __('common.date_end')) }}
                {{ Form::date('date_end', old('date_end'), ['class'=>'form-control', 'style'=>'width:100%;']) }}
              </div>
              <div class="col-md-4" style='text-align: center;'>
                {{ Form::label('search', __('common.search')) }}
                <button class="btn btn-info">
                  <i class="fa fa-search"></i> {{ __('common.search') }}
                </button>
              </div>
            </div>
          </div>
        {{ Form::close() }}
      </div>
      <div class="col-md-6">
        @if (isset($account) && $account->is_credit_card)
          {{ Form::open(['url' => '/account/'.$account->id.'/transactions/', 'method'=>'GET', 'class'=>'form-inline']) }}
            {{ Form::hidden('description', old('description')) }}
            <h4>{{__('common.filter')}}</h4>
            <div class="container-fluid" style='margin-bottom:20px;'>
              <div class="row">
                <div class="col-md-12">
                  {{ Form::label('description', __('common.description')) }}
                  {{ Form::text('description', old('description'), ['class'=>'form-control', 'style'=>'width:100%;']) }}
                </div>
              </div>
              <div class="row">
                <div class="col-md-8">
                  {{ Form::label('date_init', __('transactions.invoice')) }}
                  {{ Form::select('invoice_id', $account->getOptionsInvoices(false), old('invoice_id', isset($request->invoice_id) ? $request->invoice_id : null), ['class'=>'form-control', 'style'=>'width:100%;']) }}
                </div>
                <div class="col-md-4" style='text-align: center;'>
                  {{ Form::label('search', __('common.search')) }}
                  <button class="btn btn-info">
                    <i class="fa fa-search"></i> {{ __('common.search') }}
                  </button>
                </div>
              </div>
            </div>
          {{ Form::close() }}
        @endif
      </div>
    </div>
    <div class="row">
      <div class="container-fluid">
        <div class="row">
          {{ Form::open(['url' => (isset($account) ? '/account/'. $account->id : '' ) . '/transactions/addCategories?'.$query, 'method'=>'PUT', 'class'=>'form-inline', 'style'=>'width:100%;']) }}
            <div class="col-md-10">
              <h4>{{__('common.add_category')}}</h4>
              {{ Form::text('categories', old('categories', ''), ['class'=>'form-control', 'data-role'=>'tagsinput']) }}
            </div>
            <div class="col-md-2" style="padding-top: 40px;">
              @include('shared.submit')
            </div>
          {{ Form::close() }}
        </div>
      </div>
    </div>
  </div>
  <table class="table" style="margin-top:10px;">
    <thead>
      <tr>
        <th>{{__('common.id')}}</th>
        <th>{{__('common.date')}}</th>
        <th>{{__('transactions.invoice')}}</th>
        <th>{{__('common.description')}}</th>
        <th>{{__('common.categories')}}</th>
        <th class="text-center">{{__('transactions.value')}}</th>
        <th class="text-center">{{__('transactions.paid')}}</th>
        <th class="text-center" colspan="3">{{__('common.actions')}}</th>
      </tr>
    </thead>
    <tbody>
      @foreach($transactions as $transaction)
        <tr>
          <td>
            {{$transaction->id}}
          </td>
          <td>
            {{formatDate($transaction->date)}}
          </td>
          <td>
            @if ($transaction->account->is_credit_card)
              {{$transaction->invoice != null ? $transaction->invoice->description : '' }}
            @endif
          </td>
          <td>
            {{$transaction->description}}
          </td>
          <td>
            @if (count($transaction->categories)>0)
              <div class="bootstrap-tagsinput">
                @foreach ($transaction->categories as $category)
                  <span class="badge badge badge-info">{{$category->category->description}}</span>
                @endforeach
              </div>
            @endif
          </td>
          <td class="text-right">
            {!!format_money($transaction->value)!!}
          </td>
          <td class="text-center">
            @if (!$transaction->account->is_credit_card)
             <div class="checkbox">
                <label style="margin-bottom: 0px;">
                  <input style="vertical-align: middle;" disabled="true" type="checkbox" {{$transaction->paid?"checked='true'":""}}/>
                </label>
              </div>
            @endif
          </td>
          <td class="text-center">
            <a class="btn btn-info" title="{{__('common.repeat')}} {{__('transactions.transaction')}}" href="/account/{{$transaction->account_id}}/transaction/{{$transaction->id}}/repeat?{{ $query }}">
              <i class="fas fa-redo-alt"/></i> {{__('common.repeat')}}
            </a>
          </td>
          <td class="text-center">
            <a class="btn btn-warning" title="{{__('common.edit')}} {{__('transactions.transaction')}}" href="/account/{{$transaction->account_id}}/transaction/{{$transaction->id}}/edit?{{ $query }}">
              <i class="fa fa-edit"/></i> {{__('common.edit')}}
            </a>
          </td>
          <td class="text-center">
            <a class="btn btn-danger" title="{{__('common.remove')}} {{__('transactions.transaction')}}" href="/account/{{$transaction->account_id}}/transaction/{{$transaction->id}}/confirm?{{ $query }}">
              <i class="fa fa-trash"/></i> {{__('common.remove')}}
            </a>
          </td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="11">
          {{$transactions->links('vendor.pagination.bootstrap-4')}}
        </td>
      </tr>
    </tfoot>
  </table>
@endsection