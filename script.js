document.getElementById("spin").addEventListener("click", spinReels);
document.getElementById("increaseBet").addEventListener("click", () => adjustBet(1)); // เพิ่ม Bet
document.getElementById("decreaseBet").addEventListener("click", () => adjustBet(-1)); // ลด Bet

let bet = 1; // จำนวนเดิมพันเริ่มต้น
const maxBet = 100;
const minBet = 1;

function adjustBet(change) {
    bet = Math.min(maxBet, Math.max(minBet, bet + change));
    document.getElementById("currentBet").innerText = `Bet: $${bet}`;
}

const matchProbability = 10; // เปอร์เซ็นต์โอกาสที่ทั้ง 3 ช่องจะตรงกัน (ปรับได้)

function spinReels() {
    const symbols = ["🍒", "🍋", "🍉", "⭐", "💎"];
    const reels = ["reel1", "reel2", "reel3"];
    const spinSound = document.getElementById("spinSound");
    const winSound = document.getElementById("winSound");
    const loseSound = document.getElementById("loseSound");

    spinSound.volume = 1.0; // ตั้งค่าเสียงให้ดังเต็มที่
    spinSound.play(); // เล่นเสียงหมุน

    reels.forEach((reelId) => {
        document.getElementById(reelId).classList.add("spin");
    });

    // ลดเสียงหมุนเมื่อใกล้หยุด
    const spinDuration = 2000; // ระยะเวลาหมุนทั้งหมด (ms)
    const fadeOutDuration = 500; // ระยะเวลาที่ใช้ลดเสียง (ms)
    const fadeOutInterval = 50; // ความถี่ในการลดเสียง (ms)
    setTimeout(() => {
        let volumeStep = spinSound.volume / (fadeOutDuration / fadeOutInterval);
        let fadeOut = setInterval(() => {
            if (spinSound.volume > 0) {
                spinSound.volume = Math.max(0, spinSound.volume - volumeStep);
            } else {
                clearInterval(fadeOut);
                spinSound.pause();
                spinSound.currentTime = 0; // รีเซ็ตเสียงหมุน
            }
        }, fadeOutInterval);
    }, spinDuration - fadeOutDuration);

    setTimeout(() => {
        let results = [];
        const isMatch = Math.random() * 100 < matchProbability;

        if (isMatch) {
            const randomSymbol = symbols[Math.floor(Math.random() * symbols.length)];
            results = [randomSymbol, randomSymbol, randomSymbol];
        } else {
            for (let i = 0; i < 3; i++) {
                results.push(symbols[Math.floor(Math.random() * symbols.length)]);
            }
        }

        results.forEach((symbol, index) => {
            const reel = document.getElementById(reels[index]);
            reel.classList.remove("spin");
            reel.innerText = symbol;
        });

        const [first, second, third] = results;
        let reward = 0;

        if (first === second && second === third) {
            if (first === "🍋") {
                reward = 30 * bet; // รางวัลกลาง
                document.getElementById("result").innerText = `+${reward} (🍋) `;
                winSound.play();
            } else if (first === "🍉") {
                reward = 10 * bet; // รางวัลเล็ก
                document.getElementById("result").innerText = `+${reward} (🍉) `;
                winSound.play();
            } else {
                reward = 50 * bet; // รางวัลใหญ่
                document.getElementById("result").innerText = `+${reward}`;
                winSound.play();
            }
        } else {
            reward = -1 * bet; // เสียเงิน
            document.getElementById("result").innerText = `-${Math.abs(reward)}.00`;
            loseSound.play();
        }

        // อัปเดตยอดเงินผ่าน API
        updateBalance(reward);
    }, spinDuration);
}

function updateBalance(amount) {
    fetch("update_balance.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ amount })
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                document.getElementById("balance").innerText = `$${data.balance.toFixed(2)}`;
            } else {
                console.error("Failed to update balance:", data.message);
            }
        })
        .catch((error) => console.error("Error:", error));
}
