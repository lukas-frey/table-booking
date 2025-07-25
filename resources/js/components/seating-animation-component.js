import {gsap} from "gsap";

export default ({
                    chairCount,
                    maxChairsCount,
                    chairImage,
                    plateImages,
                }) => {
    return {
        chairCount: 0,
        chairs: [],
        plates: [],
        containerEl: null,
        chairSizeRatio: 0.8,
        hasAnimated: false,
        isAnimating: false,
        animationQueue: [],
        angleOffset: 0,

        date: null,
        time: null,
        reservationCardEl: null,
        cardRotation: 0,

        init() {
            this.containerEl = this.$el
            this.setChairCount(chairCount)

            this.$watch('$wire.guests', (value) => this.setChairCount(value))
            this.$watch('$wire.date', (value) => this.setDate(value ? new Date(value) : null))
            this.$watch('$wire.time', (value) => this.setTime(value))
            window.addEventListener("resize", () => this.handleResize())
        },

        handleResize() {
            if (this.hasAnimated && !this.isAnimating) {
                this.positionChairsAndPlates("in");  // use tighter radius

                const rect = this.containerEl.getBoundingClientRect();
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                const cardSize = Math.min(rect.width, rect.height) * 0.1;

                if (this.reservationCardEl) {
                    this.reservationCardEl.style.left = `${centerX}px`;
                    this.reservationCardEl.style.top = `${centerY}px`;
                    this.reservationCardEl.style.fontSize = `${cardSize * 0.4}px`;
                }
            }
        },


        setChairCount(count) {
            const newCount = Math.max(1, Math.min(maxChairsCount, count));
            const lastInQueue = this.animationQueue[this.animationQueue.length - 1];

            if (
                (this.animationQueue.length > 0 && lastInQueue === newCount) ||
                (!this.isAnimating && this.chairCount === newCount)
            ) return;

            const MAX_QUEUE_SIZE = 5;
            if (this.animationQueue.length >= MAX_QUEUE_SIZE) {
                this.animationQueue.shift();
            }

            this.animationQueue.push(newCount);

            if (!this.isAnimating) {
                this._processQueue();
            }
        },

        _processQueue() {
            if (this.isAnimating || this.animationQueue.length === 0) return;

            const nextCount = this.animationQueue.shift();
            this.isAnimating = true;

            const proceed = () => {
                this.chairCount = nextCount;
                this.angleOffset = Math.random() * Math.PI * 2;
                this.createChairsAndPlates();
                this.positionChairsAndPlates();
                return this.animateChairsIn();
            };

            const promise = this.hasAnimated
                ? this.animateChairsOut().then(proceed)
                : proceed();

            promise.then(() => {
                this.isAnimating = false;
                this.hasAnimated = true;
                this._processQueue();
            });
        },

        createChairsAndPlates() {
            this.chairs.forEach(el => el.remove());
            this.plates.forEach(el => el.remove());
            this.chairs = [];
            this.plates = [];

            for (let i = 0; i < this.chairCount; i++) {
                const chair = document.createElement('img');
                chair.src = chairImage;
                chair.classList.add('chair');
                chair.style.position = 'absolute';
                chair.style.transformOrigin = 'center center';
                chair.style.pointerEvents = 'none';

                const plate = document.createElement('img');
                const plateType = Math.floor(Math.random() * 3); // 0–2
                plate.src = plateImages[plateType];
                plate.classList.add('plate');
                plate.style.position = 'absolute';
                plate.style.zIndex = 7
                plate.style.transformOrigin = 'center center';
                plate.style.pointerEvents = 'none';

                this.containerEl.appendChild(chair);
                this.containerEl.appendChild(plate);

                this.chairs.push(chair);
                this.plates.push(plate);
            }
        },

        positionChairsAndPlates(mode = "default") {
            const rect = this.containerEl.getBoundingClientRect();
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            // Use smaller inward radius if mode is 'in'
            const chairRadius = rect.width * 0.5 * (mode === "in" ? 0.65 : 0.85);
            const plateRadius = rect.width * 0.5 * 0.5;
            const size = rect.width * this.chairSizeRatio;
            const plateSize = size * 0.4;

            this.chairs.forEach((chair, i) => {
                const angle = (i / this.chairCount) * Math.PI * 2 + this.angleOffset;
                const x = centerX + Math.cos(angle) * chairRadius - size / 2;
                const y = centerY + Math.sin(angle) * chairRadius - size / 2;
                const deg = angle * (180 / Math.PI) + 90;

                chair.style.width = `${size}px`;
                chair.style.height = `${size}px`;
                chair.style.left = `${x}px`;
                chair.style.top = `${y}px`;
                chair.style.transform = `rotate(${deg}deg) translate(0px, 0px)`;

                // Plate
                const plate = this.plates[i];
                const plateX = centerX + Math.cos(angle) * plateRadius - plateSize / 2;
                const plateY = centerY + Math.sin(angle) * plateRadius - plateSize / 2;

                plate.style.width = `${plateSize}px`;
                plate.style.height = `${plateSize}px`;
                plate.style.left = `${plateX}px`;
                plate.style.top = `${plateY}px`;
                plate.style.transform = `translate(0px, 0px)`;
            });
        },

        animateChairsIn() {
            const rect = this.containerEl.getBoundingClientRect();
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const fromRadius = rect.width * 0.5 * 0.85;
            const toRadius = rect.width * 0.5 * 0.65;

            const tl = gsap.timeline();

            this.chairs.forEach((chair, i) => {
                const angle = (i / this.chairCount) * Math.PI * 2 + this.angleOffset;

                const fromX = centerX + Math.cos(angle) * fromRadius - chair.offsetLeft - chair.offsetWidth / 2;
                const fromY = centerY + Math.sin(angle) * fromRadius - chair.offsetTop - chair.offsetHeight / 2;

                const toX = centerX + Math.cos(angle) * toRadius - chair.offsetLeft - chair.offsetWidth / 2;
                const toY = centerY + Math.sin(angle) * toRadius - chair.offsetTop - chair.offsetHeight / 2;

                tl.fromTo(
                    chair,
                    {x: fromX, y: fromY, opacity: 0},
                    {x: toX, y: toY, opacity: 1, duration: 0.5},
                    i * 0.05  // stagger delay for chair
                );

                const plate = this.plates[i];
                tl.fromTo(
                    plate,
                    {scale: 1.3, opacity: 0},
                    {scale: 1, opacity: 1, duration: 0.5, ease: "bounce.out"},
                    i * 0.05 + 0.05 // plate slightly after chair
                );
            });

            return tl.play();
        },

        animateChairsOut() {
            const rect = this.containerEl.getBoundingClientRect();
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const fromRadius = rect.width * 0.5 * 0.65;
            const toRadius = rect.width * 0.5 * 0.95;

            const tl = gsap.timeline();

            this.chairs.forEach((chair, i) => {
                const angle = (i / this.chairCount) * Math.PI * 2 + this.angleOffset;

                const fromX = centerX + Math.cos(angle) * fromRadius - chair.offsetLeft - chair.offsetWidth / 2;
                const fromY = centerY + Math.sin(angle) * fromRadius - chair.offsetTop - chair.offsetHeight / 2;

                const toX = centerX + Math.cos(angle) * toRadius - chair.offsetLeft - chair.offsetWidth / 2;
                const toY = centerY + Math.sin(angle) * toRadius - chair.offsetTop - chair.offsetHeight / 2;

                tl.to(
                    chair,
                    {x: toX, y: toY, opacity: 0, duration: 0.4},
                    i * 0.05
                );

                const plate = this.plates[i];
                tl.to(
                    plate,
                    {scale: 1.3, opacity: 0, duration: 0.3, ease: "power2.in"},
                    i * 0.05 + 0.05
                );
            });

            return tl.play();
        },

        // --- Reservation Card Logic ---
        setDate(newDate) {
            // Ignore if same date
            if (this.date && newDate && newDate.toISOString() === this.date.toISOString()) {
                return
            }
            this.date = newDate;
            this.updateReservationCard();
        },

        setTime(newTime) {
            this.time = newTime;

            // Ignore if time was unset
            if (! newTime) {
                return;
            }

            this.updateReservationCard();
        },

        updateReservationCard() {
            const shouldShow = this.date || this.time;

            if (shouldShow) {
                // If card doesn't exist yet, create it
                if (!this.reservationCardEl) {
                    this.createReservationCard();
                    this.animateReservationCardIn();
                } else {
                    // Always animate out → recreate → in, on any update
                    this.animateReservationCardOut(() => {
                        this.createReservationCard();
                        this.animateReservationCardIn();
                    });
                }
            } else if (this.reservationCardEl) {
                // Neither date nor time = hide card
                this.animateReservationCardOut();
            }
        },

        createReservationCard() {
            if (this.reservationCardEl) {
                this.reservationCardEl.remove();
                this.reservationCardEl = null;
            }

            const card = document.createElement("div");
            card.className = "reservation-card absolute bg-gray-800 text-white rounded-sm text-xs text-center overflow-hidden";
            card.style.zIndex = 6;
            card.style.opacity = 0;
            card.style.pointerEvents = "none";
            card.style.boxShadow = "0px 0px 1px 0px black";

            card.innerHTML = this.buildReservationCardContent();
            this.containerEl.appendChild(card);
            this.reservationCardEl = card;

            // Center it
            const rect = this.containerEl.getBoundingClientRect();
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            card.style.left = `${centerX}px`;
            card.style.top = `${centerY}px`;


            this.cardRotation = Math.random() * 40 - 30; // -30 to 10 degrees

            gsap.set(card, {
                xPercent: -50,
                yPercent: -50,
                scale: 0.5,
                rotation: this.cardRotation,
            });
        },

        buildReservationCardContent() {
            const hasTime = !!this.time;
            const hasDate = !!this.date;

            let content = ``;

            content += `<div class="text-[10px] text-white/80 px-1 text-nowrap">${hasDate ? this.date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric'
            }) : 'Reserved'}</div>`;
            content += `<div class="text-[10px] bg-white text-black px-1 text-nowrap">${hasTime ? this.time : 'Reserved'}</div>`;

            return content;
        },

        animateReservationCardIn() {
            gsap.fromTo(this.reservationCardEl,
                {scale: 1.2, opacity: 0},
                {
                    opacity: 1,
                    scale: 1,
                    duration: 0.4,
                    ease: "bounce.out",
                });
        },

        animateReservationCardOut(onComplete) {
            gsap.to(this.reservationCardEl, {
                opacity: 0,
                scale: 1.2,
                duration: 0.3,
                ease: "back.in(1.4)",
                onComplete: () => {
                    if (this.reservationCardEl) {
                        this.reservationCardEl.remove();
                        this.reservationCardEl = null;
                    }
                    if (onComplete) onComplete();
                },
            });
        },

    };
}
