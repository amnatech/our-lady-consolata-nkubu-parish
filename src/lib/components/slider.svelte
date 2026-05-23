<script>
    import Background from "$lib/assets/images/background-wallpaper.webp";
	import CONTACTS from "$lib/providers/contacts";
</script>

<main style="background-image:url({Background})">
<div class="wrapper">
    <div class="content">
        <div class="sliderCol">
            <div class="leftCol">
                <div class="punchline">
                    Visit Us, <br /> We Share In Christ
                </div>
                <div class="tagline">
                    {CONTACTS.name} Strives
                    to ensure that conservation is easy and attainable and the
                    community is conscious about environmental restoration
                </div>
            </div>

            <div class="rightCol" ></div>
        </div>
    </div>
</div>

</main>

<div class="under-col bg-consolata-blue">
    <div class="content py-4 px-2 flex items-center">
        <div class="text-[17px] text-amber-50 font-extralight">
            We Are {CONTACTS.name} We work with Kenyan communities to deliver safe schools and clean water and protect vital ecosystems through environmental education.
        </div>

        <div class="p-3">
            <button class="ui icon purple button">
                Join&nbsp;Us
            </button>
        </div>
    </div>
</div>

<style>
    main {
        min-height: 80vh;
        background-size: cover;
    }

    .wrapper{
        background: linear-gradient(to right,rgba(0, 0, 0, 0.541),rgba(0, 0, 0, 0.781),rgba(0, 0, 255, 0));
    }
  
    .sliderCol {
        display: flex;
        padding: 5px;
    }

    .leftCol {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .rightCol {
        background-position: center;
        background-size: contain;
        background-repeat: no-repeat;
        height: 80vh;

    }

    .punchline {
        font-size: 50px;
        color: #0284c7;
        font-weight: 700;
        line-height: 50px;
        text-align: left;
    }

    .tagline {
        font-weight: 300;
        font-size: 18px;
        color: rgb(255, 255, 255);
        padding-top: 1em;
        text-align: left;
    }
</style>