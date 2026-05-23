<script>

    export let organisation;
</script>
<main class="">
    <div class="content py-5">
    <div class="title text-2xl text-center font-bold mt-2">
        <span class="text-wekebio-red">Mission</span> <span class="text-consolata-blue"> Vision</span>
    </div>
    <div class="hline border-t-6 w-18 m-auto border-wekebio-purple mb-2">
        <br>
    </div>

    {#if organisation}
            <div class="flex gap-5 flex-col md:flex-row my-4">
           <div class="flex flex-1 flex-col items-center bg-white rounded gap-2 shadow-xl p-5 m-4 cursor-pointer hover:bg-blue-50">
                <div class="card-icon w-24 h-24 bg-blue-100 text-blue-700 text-4xl  text-center rounded-full flex items-center justify-center">
                    M
                </div>
                <div class="card-title py-4 font-bold text-xl">
                    Our Mission
                </div>
                <div class="card-description text-center text-slate-400 text-md">
                    {organisation?.detail.mission}
                </div>
            </div>

            <div class="flex flex-1 flex-col items-center bg-white rounded gap-2 shadow-xl p-5 m-4 cursor-pointer hover:bg-blue-50">
                <div class="card-icon w-24 h-24 bg-blue-100 text-blue-700 text-4xl  text-center rounded-full flex items-center justify-center">
                    V
                </div>
                <div class="card-title py-4 font-bold text-xl">
                    Our Vision
                </div>
                <div class="card-description text-center text-slate-400 text-md">
                    {organisation?.detail.vision}
                </div>
            </div>
    </div>
    {/if}
    </div>




</main>

<style>
    
    .content{
        background: rgba(255, 255, 255, 0.1);

    }
</style>