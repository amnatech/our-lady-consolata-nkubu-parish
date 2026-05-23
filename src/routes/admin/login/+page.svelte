<script>
	import { browser } from '$app/environment';
    import { goto } from '$app/navigation';
	import { API_BASE_URL } from '$lib/config/base_urls';
	import { post_resource } from '$lib/methods/functions';
	import { current_liu } from '$lib/methods/methods';
	import axios from 'axios';
    import pkg from 'notiflix';
	import { onMount } from 'svelte';
    const { Notify, Confirm } = pkg;

    const liu=current_liu();
    
    let username = '';
    let password = '';
    let rememberMe = false;
    let showPassword = false;
    let loading = false;


    const create_session=(token)=>{
        if(!browser){
        
           Notify.failure("Error Creating Session");

           return;
        }

         window.sessionStorage.wekebio_liu=JSON.stringify(token);
    }

    const login = async () => {
        let dt = {
            username: username,
            password: password,
        };

        loading = true;

        let url=`${API_BASE_URL}accounts/login.php`;

        let  headers={
                    "Content-Type": "application/x-www-form-urlencoded",
                }

        try {
            const resp=await post_resource("login",url,dt,headers);

            const res=resp.data;

            console.log(res);

             if (res.login) {
                Notify.success(res.message);

                create_session(res.token);

                goto('/user/dash')
            } else {
                Notify.failure(res.message);
            }

            loading=false;
        } catch (err) {
            console.log(err);
        }
    };


    onMount(()=>{
        if(liu){

            Notify.success('Login Successfull. Redirecting');

            setTimeout(()=>{
                goto('/user/dash')
            },2000)
        }
    })

</script>

<main class="fixed  w-full h-full top-0 left-0 z-1000 backdrop-blur-2xl">


<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <h1>Welcome Back</h1>
            <p>Sign in to your account to continue</p>
        </div>
        
        <form class="login-form" onsubmit={(e)=>{
            e.preventDefault();
            login();
        }}>
            <div class="input-group">
                <label for="username">Username </label>
                <input 
                    type="text" 
                    id="username" 
                    bind:value={username}
                    placeholder="john_doe"
                    required
                />
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <input 
                    type={showPassword ? 'text' : 'password'} 
                    id="password" 
                    bind:value={password}
                    placeholder="Enter your password"
                    required
                />
                <button 
                    type="button" 
                    class="password-toggle"
                    onclick={() => showPassword = !showPassword}
                >
                    {showPassword ? '👁️' : '👁️‍🗨️'}
                </button>
            </div>
            
            <div class="form-options">
                <label class="remember-me">
                    <input type="checkbox" bind:checked={rememberMe} />
                    <span>Remember me</span>
                </label>
                <a href="/forgot-password" class="forgot-password">Forgot password?</a>
            </div>
            
            <button type="submit" class="login-btn" disabled={loading}>
                {loading ? 'Signing in...' : 'Sign In'}
            </button>
    
        </form>
    </div>
</div>

</main>


<style>

    main{
        background: rgba(0, 0, 0, 0.3);
    }
    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 140px);
    }
    
    .login-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 3rem;
        width: 100%;
        max-width: 450px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }
    
    .login-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .login-header h1 {
        color: #333;
        margin: 0 0 0.5rem 0;
        font-size: 2rem;
    }
    
    .login-header p {
        color: #666;
        margin: 0;
    }
    
    .login-form {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .input-group {
        position: relative;
    }
    
    .input-group label {
        display: block;
        color: #555;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    
    .input-group input {
        width: 100%;
        padding: 0.8rem 1rem;
        border: 2px solid #e0e7ff;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: white;
    }
    
    .input-group input:focus {
        outline: none;
        border-color: var(--success-bg);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .password-toggle {
        position: absolute;
        right: 1rem;
        top: 2.3rem;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 1.2rem;
        padding: 0.2rem;
        opacity: 0.7;
        transition: opacity 0.3s ease;
    }
    
    .password-toggle:hover {
        opacity: 1;
    }
    
    .form-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .remember-me {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #555;
        cursor: pointer;
    }
    
    .remember-me input {
        margin: 0;
    }
    
    .forgot-password {
        color: #49a994;
        text-decoration: none;
        font-size: 0.9rem;
    }
    
    .forgot-password:hover {
        text-decoration: underline;
    }
    
    .login-btn {
        padding: 1rem;
        background: linear-gradient(45deg, #9fea66, #4b92a2);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .login-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 232, 234, 0.3);
    }
    
    .login-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
</style>