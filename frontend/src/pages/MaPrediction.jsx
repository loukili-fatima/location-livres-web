import { useEffect, useState } from "react";
import api from "../api";

function MaPrediction() {
  const [prediction, setPrediction] = useState(null);
  const [chargement, setChargement] = useState(true);
  const [erreur, setErreur] = useState("");

  useEffect(() => {
    api.get("/ma-prediction")
      .then((response) => {
        setPrediction(response.data);
        setChargement(false);
      })
      .catch(() => {
        setErreur("Impossible de charger votre prediction.");
        setChargement(false);
      });
  }, []);

  if (chargement) return <p style={{ padding: "20px" }}>Chargement...</p>;
  if (erreur) return <p style={{ padding: "20px", color: "red" }}>{erreur}</p>;

  return (
    <div style={{ maxWidth: "600px", margin: "60px auto", fontFamily: "sans-serif", textAlign: "center" }}>
      <h1>Ma prediction</h1>
      <div style={{ border: "1px solid #ddd", borderRadius: "12px", padding: "40px", marginTop: "24px" }}>
        <p style={{ fontSize: "16px", color: "#666", marginBottom: "8px" }}>
          Notre modele predit que vous aimez :
        </p>
        <div style={{ fontSize: "36px", fontWeight: "bold", color: "#2563eb", marginBottom: "16px" }}>
          {prediction.categorie}
        </div>
        <div style={{ fontSize: "14px", color: "#666" }}>
          Confiance : {prediction.confiance.toFixed ? prediction.confiance.toFixed(2) : prediction.confiance}%
        </div>
      </div>
    </div>
  );
}

export default MaPrediction;
