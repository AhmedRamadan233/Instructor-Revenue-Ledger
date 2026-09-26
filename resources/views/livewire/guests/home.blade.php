<div>
    <div class="mb-4">
        <h1 class="h3 mb-2">Welcome</h1>
        <p class="text-muted">
            Choose a subscription plan, or browse the courses teachers publish for students.
        </p>
    </div>

    <div class="row g-3">
        <div class="col-12 col-md-6" x-data="{ open: false }">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 card-title">Plans to Subscribe</h2>
                    <p class="card-text text-muted">
                        Packages you can subscribe to. One plan unlocks courses from all teachers.
                    </p>

                    <button type="button" class="btn btn-sm btn-outline-secondary mb-3" @click="open = !open">
                        <span x-text="open ? 'Hide details' : 'Show details'"></span>
                    </button>

                    <p class="small text-muted" x-show="open" x-transition>
                        Pick Monthly, Yearly, or another billing type from one select — every plan card updates together.
                    </p>

                    <a href="{{ route('guest.plans') }}" class="btn btn-primary">View Plans</a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6" x-data="{ open: false }">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 card-title">Courses for Students</h2>
                    <p class="card-text text-muted">
                        Courses published by teachers. Students access them after an active subscription.
                    </p>

                    <button type="button" class="btn btn-sm btn-outline-secondary mb-3" @click="open = !open">
                        <span x-text="open ? 'Hide details' : 'Show details'"></span>
                    </button>

                    <p class="small text-muted" x-show="open" x-transition>
                        Search courses live with Livewire. Results update as you type.
                    </p>

                    <a href="{{ route('guest.courses') }}" class="btn btn-outline-primary">View Courses</a>
                </div>
            </div>
        </div>
    </div>
</div>
