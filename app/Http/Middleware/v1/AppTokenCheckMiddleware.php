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

            if( strcmp( $app_token, 'sJj&hrdeBghk9JDUjctWJ+4d9bb5n%jCYTqyEs5B%_PC9+bWyJWM7@rkdxHC^!Qf#T2cnyaJfRDMbJsZ-b=U$9T2ArFvhsnXdQbt7QCmPq?VP5YE5pwS5CWsqKEQ9H5KTBPr^v=2s7J75@RE8m3=R*ysH5nMV$&FF=2yFrGxqV984bg#2DsRaGAu%^YV@sFW#%6N=5@=jBBsWBpuT28zv-V5rdY!e9%DAADKS^Zgz&w&FyvUp#W$LFuWzmaA5U&_8w^c4RLCj+zm!&*QjQ!MVj*7c_39eu7CpKvJRk@%-!v!?HnCprFcC2X4WDB=mLxZ225!BPmg*QaRp?Dud!RL&+x#HGC$f94yWEyRkDLHEUXDEVe&-9FKz&2q##RsN-SBnEF&RF^?Epe@Kf9Q@49SUpem72cAL6=euay36xYXDZ-9=5u%ak6$!q9f36$Lfs3_GcgZAww6fTH-!5yy5bU?7d$rMU?KkEHL?C^$8Q3Lxu2Jp_src!q!+%NUcTrVTECa?=%sDNapR$DkWHz=xcdss6HzJ3BSGQsVxN2Jj==s-?CCaj*kgRC3gCYhfgwQMX+P&Rnd8CbJ_=2@F9_8*Bq!at%Vs?^47Y%_E^8BZMAv9zf&4LK-5*Xcape4Ef#P7v%vX36ad8=fP+=64yFx-xX95HDYuuK7D!autzJm-xb=K*BJdX%t7&$H+xrPBWwT@LANdkcEyA^-#Q%YuDDn*8Lxm%_z&c6kq7tJe6x67fFsh?e$TEme385M%Z7cp&#2cLk_h$5+hk+ZVCy^up^%Dkn8DQENY?dCL&ZUr+fV=^dqDGxbWD!yYjp4XyGn*jt2^D8k+WLDkk^_qqnUbh?f2X&2t@3YGMdVXT7Nz-xvs+N#Bc!5EESb^W#v^w7kLE=xcyb3_bdFdHuEC5bt6PrEeU#bgYRMe%M$89=ePHB89y=%#JGQ2!$Ze3TxsHX?EaXU+@3u?nHFjhQ%Pqz%6bktEbpeNLvQ69HDNGB@5-=Fq!$ufwndGS5!ka6*tU9GeWZ@gbg%') == 0 ){

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
