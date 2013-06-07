/* Dipendenti */

CREATE VIEW Comandanti AS
SELECT d.matricola, a.nome, a.cognome, a.sesso, a.nascita, c.nome AS Compagnia
FROM Dipendenti d NATURAL JOIN Anagrafiche a NATURAL JOIN Compagnie c
WHERE d.grado='comandante'

CREATE VIEW ViewViaggiDiretti AS
SELECT vi.idViaggio, a1.nome AS da, a2.nome AS a, vi.giorno, vo.oraP, vo.oraA, timediff(vo.oraP, vo.oraA) AS durata, co.nome AS Compagnia, vi.InseritoDa AS Amministratore
FROM Viaggi vi, Tratte t, Aeroporti a1, Aeroporti a2, CompagnieViaggi cvi, Compagnie co, Voli vo
WHERE vi.idTratta=t.idTratta AND t.da=a1.idAeroporto AND t.da=a2.idAeroporto AND vi.idViaggio=cvi.idViaggio
	AND cvi.idVolo=vo.numero AND vi.diretto=TRUE AND co.idCompagnia=cvi.idCompagniaEsec AND vi.stato='previsto'
	
