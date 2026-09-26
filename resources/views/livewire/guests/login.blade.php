<div>
    <div class="row justify-content-center">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header">Login</div>
                <div class="card-body">
                    <form wire:submit="login">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input
                                id="email"
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                wire:model="email"
                                autocomplete="username"
                                required
                                autofocus
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input
                                id="password"
                                type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                wire:model="password"
                                autocomplete="current-password"
                                required
                            >
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input
                                id="remember"
                                type="checkbox"
                                class="form-check-input"
                                wire:model="remember"
                            >
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="login">Login</span>
                            <span wire:loading wire:target="login">Logging in...</span>
                        </button>
                    </form>

                    <hr>

                    <p class="small text-muted mb-0">
                        Demo accounts password: <code>password</code><br>
                        manager@example.com · teacher1@example.com · student1@example.com
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
