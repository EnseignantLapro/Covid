<script>

    // Développé par G. Febvin

    function hitAnimation ( event ) {

        const weaponType = document.querySelector(".Arme").innerText
            , eventFrame = document.querySelector(".effect")
            , soundPath  = "Assets/Sounds";

        const rand = ( x ) => ~~( Math.random() * x ) + 1;

        const { clientX, clientY } = event;

        if ( [ "Glaive", "Épée" ].some( w => weaponType.includes( w ) ) ) {

            eventFrame.style.top  = `${clientY + window.scrollY}px`;
            eventFrame.style.left = `${clientX}px`;
            eventFrame.classList.add("sword-slash");
            new Audio(`${soundPath}/Sword/SwordSound${rand(3)}.mp3`).play();

        }

        eventFrame.classList.add("play");
        eventFrame.onanimationend = () => eventFrame.classList.remove("play");

    }

</script>