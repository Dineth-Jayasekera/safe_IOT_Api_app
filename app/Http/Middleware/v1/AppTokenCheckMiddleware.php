<?php

namespace App\Http\Middleware\v1;

use Closure;

class AppTokenCheckMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        /** @var $allHeaders Get All Headers */

        $allHeaders = $request->headers->all();

        /** Validate App Token in Header */

        if( isset( $allHeaders['app-token'] ) ){

            /** If token mismatch return */

            $app_token = $allHeaders['app-token'][0];

            if( strcmp( $app_token, '$*P?vm!QT?_sX=hv+jAsFgxmc2EFB!p2ZE=yWDBDMmc8bvxZX$s$Rh5^*95L6vAJNKVzVnRU2Ee%FXa39!+4j-#QtR2PJ+Tgu2XvU+H^4f@*$^ZCd=duMz+m9Q_+K$6M*g4fvphRat_k8!2&Am&3ud%$x&Wy9mzCKt%^j5KjXEsdKcBBw^QD!Qt^LGz-URscWVTfFyghW%c+8=GFeRWgny^g2KcEBpfJfn?qu_k23+2!BY_5mBpxtMv@_XQG#6+C') == 0 ){

                return $next($request);

            }else{

                /** Return Error */

                return redirect('api/v1/login-error');
            }

        }else{

            /** Return Error */

            return redirect('api/v1/login-error');

        }

    }
}
