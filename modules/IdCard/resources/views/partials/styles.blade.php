@once
<style>
    /* ID-1 card (85.6 × 54 mm); sizes in cqw so the card scales as a whole. */
    .id-card {
        container-type: inline-size;
        width: 100%;
        max-width: 480px;
        aspect-ratio: 85.6 / 54;
        border-radius: 3.5cqw;
        overflow: hidden;
        position: relative;
        box-shadow: 0 .5rem 1.5rem rgba(0, 0, 0, .15);
        background: var(--card-bg);
        color: var(--card-text);
        font-family: inherit;
    }
    .id-card-inner { position: absolute; inset: 0; display: flex; flex-direction: column; }
    .id-card-head { display: flex; align-items: center; gap: 2.5cqw; padding: 2.8cqw 4cqw; background: var(--card-accent); color: #fff; }
    .id-card-logo { height: 7cqw; width: auto; max-width: 18cqw; object-fit: contain; background: #fff; border-radius: 1.2cqw; padding: .6cqw; }
    .id-card-org { font-size: 3.4cqw; font-weight: 700; line-height: 1.15; }
    .id-card-type { margin-left: auto; font-size: 2.6cqw; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; opacity: .95; white-space: nowrap; }
    .id-card-body { flex: 1; display: flex; gap: 4cqw; padding: 3.5cqw 4cqw 3cqw; min-height: 0; }
    /* align-self keeps the 3:4 box: stretched to the body's height, the photo turned tall and narrow. */
    .id-card-photo { width: 22cqw; height: auto; aspect-ratio: 3 / 4; flex: none; align-self: flex-start; box-sizing: border-box; border-radius: 1.8cqw; object-fit: cover; background: rgba(0, 0, 0, .06); display: flex; align-items: center; justify-content: center; border: .4cqw solid var(--card-accent); }
    .id-card-photo i { font-size: 10cqw; opacity: .35; }
    .id-card-main { flex: 1; min-width: 0; display: flex; flex-direction: column; }
    .id-card-name { font-size: 5.2cqw; font-weight: 700; line-height: 1.1; overflow-wrap: anywhere; }
    .id-card-title { font-size: 2.9cqw; color: var(--card-accent); font-weight: 600; margin-top: .6cqw; }
    .id-card-fields { margin: 2cqw 0 0; font-size: 2.6cqw; line-height: 1.35; }
    .id-card-fields dt { font-weight: 400; opacity: .65; font-size: 2.1cqw; text-transform: uppercase; letter-spacing: .04em; margin-top: .8cqw; }
    .id-card-fields dd { margin: 0; font-weight: 600; overflow-wrap: anywhere; }
    .id-card-foot { margin-top: auto; display: flex; align-items: flex-end; gap: 2cqw; }
    .id-card-number { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 3cqw; font-weight: 700; letter-spacing: .05em; }
    .id-card-since { font-size: 2.2cqw; opacity: .7; }
    .id-card-qr { margin-left: auto; width: 17cqw; flex: none; background: #fff; border-radius: 1.2cqw; padding: .8cqw; line-height: 0; }
    .id-card-qr svg { width: 100%; height: auto; }
    .id-card-footer { font-size: 2cqw; padding: 1.4cqw 4cqw; opacity: .75; border-top: .25cqw solid rgba(0, 0, 0, .08); }
    .id-card-void { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, .6); }
    .id-card-void span { transform: rotate(-12deg); border: .8cqw solid #d63939; color: #d63939; font-size: 7cqw; font-weight: 800; padding: 1cqw 4cqw; border-radius: 2cqw; letter-spacing: .1em; }

    @media print {
        @page { size: 85.6mm 54mm; margin: 0; }
        body.id-card-printing * { visibility: hidden !important; }
        body.id-card-printing .id-card.is-printing,
        body.id-card-printing .id-card.is-printing * { visibility: visible !important; }
        body.id-card-printing .id-card.is-printing { position: fixed; left: 0; top: 0; width: 85.6mm; max-width: none; border-radius: 0; box-shadow: none; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
<script>
    function printIdCard(id) {
        const card = document.getElementById(id);
        document.body.classList.add('id-card-printing');
        card.classList.add('is-printing');
        window.addEventListener('afterprint', () => {
            document.body.classList.remove('id-card-printing');
            card.classList.remove('is-printing');
        }, { once: true });
        window.print();
    }
</script>
@endonce
