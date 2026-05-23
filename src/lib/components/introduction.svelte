<script>

    export let organisation;

</script>

<main class="p-5">
    <div class="content">
        <div class="py-5">
            <div class="title text-3xl text-center font-extrabold mt-2">
            <span class="text-wekebio-red">Welcome To</span> <span class="text-consolata-blue">{organisation?.name}</span>
        </div>
        <div class="hline border-t-6 w-18 m-auto border-wekebio-purple mb-2">
            <br>
        </div>
        </div>

        <div class="intro p-2  md:max-w-[60vw] m-auto text-center">
        {@html organisation?.detail.introduction}
        </div>
    </div>

</main>

<style>
    .intro{
        line-height: 2.5em;
    }
</style>