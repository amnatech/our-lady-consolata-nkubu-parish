<script>
    import Background from "$lib/assets/images/top-slider.jpeg";
	import CONTACTS from "$lib/providers/contacts";

    export let title="Page Title";

    export let tagline="Page Tagline";

</script>

<main style="background-image:url({Background})">
    <div class="wrapper backdrop:blur-2xl">
    <div class="content flex items-center justify-center ">
        <div class="sliderCol">
                <div class="punchline capitalize flex-auto ">
                    {@html title}
                </div>
                <div class="tagline capitalize">
                    {@html tagline}
                </div>
        </div>
    </div>
    </div>

</main>

<style>
    main {
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        
    }

    .wrapper{
        backdrop-filter: blur(10px);
        /* background: #000; */
        height: 100%;
        height: 30vh;


    }

    .content{
        min-height: 30vh;
    }

    .punchline {
        font-size: 30px;
        font-weight: 700;
        line-height: 50px;
        text-align: center;
        flex:1;
        color: wheat;
    }

    .tagline {
        font-weight: 300;
        font-size: 17px;
        color: wheat;
        padding-top: .5em;
        text-align: center;
    }

    @media only screen and (min-width:768px){
        main {
            min-height: 30vh;
            background-size: cover;
        }

        .wrapper{
        backdrop-filter: blur(10px);
        /* background: #000; */
        height: 100%;
        height: 30vh;


    }

    .punchline {
        font-size: 30px;
        font-weight: 700;
        line-height: 50px;
        text-align: center;
        flex:1
    }

    .tagline {
        font-weight: 300;
        font-size: 18px;
        padding-top: .5em;
        text-align: center;
    }

    }
</style>