async function loadTXT(path) {
    const res = await fetch(path);
    return res.text();
}

async function loadGames() {
    const text = await loadTXT("./database/partite.txt");
    return text.trim().split("\n").map(l => {
        const [id, t1, t2, g1, g2, date] = l.split(";");
        return {
            id: Number(id),
            id1: Number(t1),
            id2: Number(t2),
            gol1: g1 === "null" ? null : Number(g1),
            gol2: g2 === "null" ? null : Number(g2),
            orario: date
        };
    });
}

async function loadSquadre() {
    const text = await loadTXT("./database/squadre.txt");
    return text.trim().split("\n").map(l => {
        const [id, g, p, d, nome] = l.split(";");
        return { id: Number(id), nome, girone: Number(g), punti: Number(p), differenza_reti: Number(d) };
    });
}

async function loadGironi() {
    const text = await loadTXT("./database/gironi.txt");
    return text.trim().split("\n").map(l => {
        const [id, nome] = l.split(";");
        return { id: Number(id), nome };
    });
}

async function loadCalciatori() {
    const text = await loadTXT("./database/calciatori.txt");
    return text.trim().split("\n").map(l => {
        const [id, nome, gol, sq] = l.split(";");
        return { id: Number(id), nome, gol: Number(gol), squadra: Number(sq) };
    });
}

async function loadMarcatori() {
    const text = await loadTXT("./database/marcatori.txt");
    return text.trim().split("\n").map(l => {
        const [id, partita, calciatore, gol] = l.split(";");
        return {
            partita: Number(partita),
            calciatore: Number(calciatore),
            gol: Number(gol)
        };
    });
}