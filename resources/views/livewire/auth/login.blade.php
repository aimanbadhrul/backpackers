<div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-4">Login</h2>

    <form wire:submit.prevent="login">
        <div class="mb-4">
            <label class="block text-sm font-medium">Email</label>
            <input type="email" wire:model="email" class="w-full p-2 border rounded">
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Password</label>
            <input type="password" wire:model="password" class="w-full p-2 border rounded">
            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4 flex items-center">
            <input type="checkbox" wire:model="remember" class="mr-2">
            <span>Remember me</span>
        </div>

        <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded">Login</button>
    </form>
</div>

