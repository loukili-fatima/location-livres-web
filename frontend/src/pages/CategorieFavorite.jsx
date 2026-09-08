import { useEffect, useState } from "react";
import api from "../api";

function CategorieFavorite() {
  const [data, setData] = useState(null);
  const [chargement, setChargement] = useState(true);
  const [erreur, setErreur] = useState("");

  useEffect(() => {
    api.get("/categorie-favorite")
      .then((response) => {
        setData(response.data);
        setChargement(false);
      })
      .catch(() => {
        setErreur("Impossible de charger vos suggestions.");
        setChargement(false);
      });
  }, []);

  if (chargement) return <p style={{ padding: "20px" }}>Chargement...</p>;
  if (erreur) return <p style={{ padding: "20px", color: "red" }}>{erreur}</p>;

  return (
    <div style={{ maxWidth: "900px", margin: "40px auto", fontFamily: "sans-serif" }}>
      <h1>Ma categorie favorite</h1>

      {!data.epuisee && (
        <>
          <p style={{ color: "#666" }}>
            Voici les livres disponibles dans votre categorie favorite : <strong>{data.categorie}</strong>
          </p>
          <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(250px, 1fr))", gap: "16px", marginTop: "16px" }}>
            {data.livres.map((livre) => (
              <div key={livre.id} style={{ border: "1px solid #444", borderRadius: "8px", padding: "16px" }}>
                <h3 style={{ margin: "0 0 8px 0" }}>{livre.titre}</h3>
                <p style={{ margin: 0, color: "#999" }}>{livre.author?.nom}</p>
              </div>
            ))}
          </div>
        </>
      )}

      {data.epuisee && data.categorie_proche && (
        <>
          <p style={{ color: "#e5a000" }}>
            Vous avez deja loue tous les livres disponibles de votre categorie favorite (<strong>{data.categorie}</strong>).
          </p>
          <p style={{ color: "#666" }}>
            D'autres utilisateurs qui aiment cette categorie apprecient aussi : <strong>{data.categorie_proche}</strong>
            {data.score_similarite ? ` (similarite : ${(data.score_similarite * 100).toFixed(0)}%)` : ""}
          </p>
          <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(250px, 1fr))", gap: "16px", marginTop: "16px" }}>
            {data.livres.map((livre) => (
              <div key={livre.id} style={{ border: "1px solid #444", borderRadius: "8px", padding: "16px" }}>
                <h3 style={{ margin: "0 0 8px 0" }}>{livre.titre}</h3>
                <p style={{ margin: 0, color: "#999" }}>{livre.author?.nom}</p>
              </div>
            ))}
          </div>
        </>
      )}

      {data.epuisee && !data.categorie_proche && (
        <p style={{ color: "#666" }}>
          Vous avez deja loue tous les livres disponibles de votre categorie favorite, et aucune suggestion n'est disponible pour le moment.
        </p>
      )}
    </div>
  );
}

export default CategorieFavorite;