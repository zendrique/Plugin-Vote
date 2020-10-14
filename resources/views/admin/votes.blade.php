@extends('admin.layouts.admin')

@section('title', trans('vote::admin.votes.title'))

@section('content')
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ trans('vote::admin.votes.votes') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $votesCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-vote-yea fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Month -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ trans('vote::admin.votes.month') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $votesCountMonth }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-vote-yea fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Week -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ trans('vote::admin.votes.week') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $votesCountWeek }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-vote-yea fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Day -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ trans('vote::admin.votes.day') }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $votesCountDay }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-vote-yea fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                {{ trans('vote::messages.sections.top') }}
            </h6>
        </div>
        <div class="card-body">
            <ul class="nav nav-pills mb-3" id="voteTabs" role="tablist">
                @foreach($votes as $voteDate => $userVotes)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link @if($voteDate === $now) active @endif" id="voteTab{{ $loop->index }}" data-toggle="tab" href="#votesPane{{ $loop->index }}" role="tab" aria-controls="votesPane{{ $loop->index }}" aria-selected="{{ $voteDate === $now ? 'true' : 'false' }}">
                            {{ $voteDate }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content">
                @foreach($votes as $voteDate => $userVotes)
                    <div class="tab-pane fade @if($voteDate === $now) show active @endif" id="votesPane{{ $loop->index }}" aria-labelledby="votesTab{{ $loop->index }}">
                        @if(! $userVotes->isEmpty())
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">{{ trans('messages.fields.name') }}</th>
                                    <th scope="col">{{ trans('vote::messages.fields.votes') }}</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($userVotes as $id => $vote)
                                    <tr>
                                        <th scope="row">#{{ $id }}</th>
                                        <td>{{ $vote->user->name }}</td>
                                        <td>{{ $vote->votes }}</td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-info text-center" role="alert">
                                <i class="fas fa-info-circle"></i> {{ trans('vote::admin.votes.empty') }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    <div class="row">
        <!-- Area Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ trans('vote::admin.votes.votes') }}</h6>
                </div>
                <div class="card-body">
                    <div class="tab-content mb-3">
                        <div class="tab-pane fade show active" id="monthlyChart" role="tabpanel" aria-labelledby="monthlyChartTab">
                            <div class="chart-area">
                                <canvas id="votesPerMonthsChart"></canvas>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="dailyChart" role="tabpanel" aria-labelledby="dailyChartTab">
                            <div class="chart-area">
                                <canvas id="votesPerDaysChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="monthlyChartTab" data-toggle="pill" href="#monthlyChart" role="tab" aria-controls="monthlyChart" aria-selected="true">
                                {{ trans('messages.range.months') }}
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="dailyChartTab" data-toggle="pill" href="#dailyChart" role="tab" aria-controls="dailyChart" aria-selected="false">
                                {{ trans('messages.range.days') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-scripts')
    <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('admin/js/charts.js') }}"></script>
    <script>
        createLineChart('votesPerMonthsChart', @json($votesPerMonths), '{{ trans('vote::admin.votes.votes') }}');
        createLineChart('votesPerDaysChart', @json($votesPerDays), '{{ trans('vote::admin.votes.votes') }}');
    </script>
@endpush
