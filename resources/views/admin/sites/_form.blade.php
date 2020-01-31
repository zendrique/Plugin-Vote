@csrf

<div class="form-group">
    <label for="nameInput">{{ trans('messages.fields.name') }}</label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="nameInput" name="name" value="{{ old('name', $site->name ?? '') }}" required>

    @error('name')
    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

<div class="form-group">
    <label for="urlInput">{{ trans('messages.fields.url') }}</label>
    <input type="url" class="form-control @error('url') is-invalid @enderror" id="urlInput" name="url" value="{{ old('url', $site->url ?? '') }}" required>

    @error('url')
    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

<div class="form-group">
    <label for="delayInput">{{ trans('vote::admin.sites.delay') }}</label>
    <input type="number" min="0" class="form-control @error('vote_delay') is-invalid @enderror" id="delayInput" name="vote_delay" value="{{ old('vote_delay', $site->vote_delay ?? '') }}" required>

    @error('vote_delay')
    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

<div class="form-group">
    <label>{{ trans('vote::messages.fields.rewards') }}</label>

    <div class="card">
        <div class="card-body">
            @forelse($rewards as $reward)
                <div class="form-group custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="rewards{{ $reward->id }}" name="rewards[{{ $reward->id }}]" @if(isset($site) && $site->rewards->contains($reward)) checked @endif>
                    <label class="custom-control-label" for="rewards{{ $reward->id }}">{{ $reward->name }}</label>
                </div>
            @empty
                <a href="{{ route('vote.admin.rewards.create') }}" class="btn btn-success btn-sm" target="_blank"><i class="fas fa-plus"></i> {{ trans('messages.actions.add') }}</a>
            @endforelse
        </div>
    </div>

    @error('rewards')
    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
    @enderror
</div>

<div class="form-group custom-control custom-switch">
    <input type="checkbox" class="custom-control-input" id="enableSwitch" name="is_enabled" @if($site->is_enabled ?? true) checked @endif>
    <label class="custom-control-label" for="enableSwitch">{{ trans('vote::admin.sites.enable') }}</label>
</div>