import { useEffect, useState } from "react";
import api from "../api";

function Livres() {
  const [livres, setLivres] = useState([]);
  const [chargement, setChargement] = useState(true);
  const [erreur, setErreur] = useState("");
  const [locationEnCours, setLocationEnCours] = useState(null);
  const [messageLocation, setMessageLocation] = useState({});

  useEffect(() => {
    api.get("/books")
      .then((response) => {
        setLivres(response.data);
        setChargement(false);
      })
      .catch(() => {
        setErreur("Impossible de charger les livres.");
        setChargement(false);
      });
  }, []);

  const louerLivre = (livreId) => {
    setLocationEnCours(livreId);
    setMessageLocation((prev) => ({ ...prev, [livreId]: "" }));

    api.post("/rentals", { book_id: livreId })
      .then(() => {
        setLivres((prev) =>
          prev.map((livre) =>
            livre.id === livreId ? { ...livre, disponible: false } : livre
          )
        );
        setLocationEnCours(null);
      })
      .catch((error) => {
        const message =
          (error.response && error.response.data && error.response.data.message) ||
          "Impossible de louer ce livre.";
        setMessageLocation((prev) => ({ ...prev, [livreId]: message }));
        setLocationEnCours(null);
      });
  };

  if (chargement) return <p style={{ padding: "20px" }}>Chargement...</p>;
  if (erreur) return <p style={{ padding: "20px", color: "red" }}>{erreur}</p>;

  return (
    <div style={{ maxWidth: "900px", margin: "40px auto", fontFamily: "sans-serif" }}>
      <h1>Catalogue de livres</h1>
      <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(250px, 1fr))", gap: "16px" }}>
        {livres.map((livre) => (
          <div key={livre.id} style={{ border: "1px solid #ddd", borderRadius: "8px", padding: "16px" }}>
            <h3 style={{ margin: "0 0 8px 0" }}>{livre.titre}</h3>
            <p style={{ margin: "0 0 8px 0", color: "#666" }}>{livre.author?.nom}</p>
            <span style={{
              fontSize: "12px",
              padding: "4px 8px",
              borderRadius: "4px",
              backgroundColor: livre.disponible ? "#d1fae5" : "#fee2e2",
              color: livre.disponible ? "#065f46" : "#991b1b",
            }}>
              {livre.disponible ? "Disponible" : "Deja loue"}
            </span>

            {Boolean(livre.disponible) && (
              <div style={{ marginTop: "12px" }}>
                <button
                  onClick={() => louerLivre(livre.id)}
                  disabled={locationEnCours === livre.id}
                  style={{
                    padding: "6px 12px",
                    borderRadius: "4px",
                    border: "none",
                    backgroundColor: "#2563eb",
                    color: "#fff",
                    cursor: locationEnCours === livre.id ? "default" : "pointer",
                    marginTop: "8px",
                  }}
                >
                  {locationEnCours === livre.id ? "Location..." : "Louer ce livre"}
                </button>
                {messageLocation[livre.id] && (
                  <p style={{ color: "red", fontSize: "12px", marginTop: "4px" }}>
                    {messageLocation[livre.id]}
                  </p>
                )}
              </div>
            )}
          </div>
        ))}
      </div>
    </div>
  );
}

export default Livres;
