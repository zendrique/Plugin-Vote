@extends('admin.layouts.admin')

@section('title', trans('vote::admin.votes.title'))

@section('content')
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
@endsection
