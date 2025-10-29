namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (auth()->check() && auth()->user()->peran === $role) {
            return $next($request);
        }
        return redirect('/dashboard');
    }
}
