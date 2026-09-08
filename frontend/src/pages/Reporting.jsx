import { useEffect, useState } from "react";
import api from "../api";

function Reporting() {
  const [data, setData] = useState(null);
  const [chargement, setChargement] = useState(true);
  const [erreur, setErreur] = useState("");

  useEffect(() => {
    api.get("/reporting")
      .then((response) => {
        setData(response.data);
        setChargement(false);
      })
      .catch(() => {
        setErreur("Impossible de charger les statistiques (acces reserve aux admins).");
        setChargement(false);
      });
  }, []);

  if (chargement) return <p style={{ padding: "20px" }}>Chargement...</p>;
  if (erreur) return <p style={{ padding: "20px", color: "red" }}>{erreur}</p>;

  const maxParMois = Math.max(...data.locations_par_mois.map((m) => m.total), 1);

  return (
    <div style={{ maxWidth: "900px", margin: "40px auto", fontFamily: "sans-serif" }}>
      <h1>Reporting</h1>

      <div style={{ display: "grid", gridTemplateColumns: "repeat(3, 1fr)", gap: "16px", marginBottom: "32px" }}>
        <div style={{ border: "1px solid #ddd", borderRadius: "8px", padding: "16px", textAlign: "center" }}>
          <div style={{ fontSize: "28px", fontWeight: "bold" }}>{data.total_livres}</div>
          <div style={{ color: "#666" }}>Total livres</div>
        </div>
        <div style={{ border: "1px solid #ddd", borderRadius: "8px", padding: "16px", textAlign: "center" }}>
          <div style={{ fontSize: "28px", fontWeight: "bold" }}>{data.livres_disponibles}</div>
          <div style={{ color: "#666" }}>Livres disponibles</div>
        </div>
        <div style={{ border: "1px solid #ddd", borderRadius: "8px", padding: "16px", textAlign: "center" }}>
          <div style={{ fontSize: "28px", fontWeight: "bold" }}>{data.taux_disponibilite}%</div>
          <div style={{ color: "#666" }}>Taux de disponibilite</div>
        </div>
      </div>

      <h2>Locations par mois</h2>
      <div style={{ display: "flex", alignItems: "flex-end", gap: "8px", height: "160px", marginBottom: "32px", borderBottom: "1px solid #ddd", paddingBottom: "8px" }}>
        {data.locations_par_mois.map((m) => (
          <div key={m.mois} style={{ flex: 1, textAlign: "center" }}>
            <div
              style={{
                backgroundColor: "#2563eb",
                borderRadius: "4px 4px 0 0",
                height: `${(m.total / maxParMois) * 120}px`,
                marginBottom: "4px",
              }}
              title={`${m.total} locations`}
            ></div>
            <div style={{ fontSize: "11px", color: "#666" }}>{m.mois}</div>
            <div style={{ fontSize: "12px", fontWeight: "bold" }}>{m.total}</div>
          </div>
        ))}
      </div>

      <h2>Livres les plus populaires</h2>
      <table style={{ width: "100%", borderCollapse: "collapse", marginBottom: "32px" }}>
        <thead>
          <tr style={{ textAlign: "left", borderBottom: "2px solid #ddd" }}>
            <th style={{ padding: "8px" }}>Titre</th>
            <th style={{ padding: "8px" }}>Nombre de locations</th>
          </tr>
        </thead>
        <tbody>
          {data.livres_populaires.map((item) => (
            <tr key={item.book_id} style={{ borderBottom: "1px solid #eee" }}>
              <td style={{ padding: "8px" }}>{item.book?.titre}</td>
              <td style={{ padding: "8px" }}>{item.total}</td>
            </tr>
          ))}
        </tbody>
      </table>

      <h2>Penalites par utilisateur</h2>
      <table style={{ width: "100%", borderCollapse: "collapse" }}>
        <thead>
          <tr style={{ textAlign: "left", borderBottom: "2px solid #ddd" }}>
            <th style={{ padding: "8px" }}>Utilisateur</th>
            <th style={{ padding: "8px" }}>Penalite totale</th>
          </tr>
        </thead>
        <tbody>
          {data.penalites_par_utilisateur.map((item) => (
            <tr key={item.user_id} style={{ borderBottom: "1px solid #eee" }}>
              <td style={{ padding: "8px" }}>{item.nom}</td>
              <td style={{ padding: "8px" }}>{item.penalite_totale.toFixed(2)} EUR</td>
            </tr>
          ))}
        </tbody>
      </table>
          <h2>Risque de retard (locations en cours)</h2>
      <table style={{ width: "100%", borderCollapse: "collapse" }}>
        <thead>
          <tr style={{ textAlign: "left", borderBottom: "2px solid #ddd" }}>
            <th style={{ padding: "8px" }}>Utilisateur</th>
            <th style={{ padding: "8px" }}>Livre</th>
            <th style={{ padding: "8px" }}>Retour prevu</th>
            <th style={{ padding: "8px" }}>Risque</th>
          </tr>
        </thead>
        <tbody>
          {data.risques_retard.map((item) => {
            const couleur =
              item.risque_retard >= 60 ? "#d9534f" :
              item.risque_retard >= 35 ? "#f0ad4e" :
              "#5cb85c";
            return (
              <tr key={item.rental_id} style={{ borderBottom: "1px solid #eee" }}>
                <td style={{ padding: "8px" }}>{item.user}</td>
                <td style={{ padding: "8px" }}>{item.livre}</td>
                <td style={{ padding: "8px" }}>{item.date_retour_prevue}</td>
                <td style={{ padding: "8px", color: couleur, fontWeight: "bold" }}>
                  {item.risque_retard}%
                </td>
              </tr>
            );
          })}
        </tbody>
      </table>
          <h2>Risque de retard (locations en cours)</h2>
      <table style={{ width: "100%", borderCollapse: "collapse" }}>
        <thead>
          <tr style={{ textAlign: "left", borderBottom: "2px solid #ddd" }}>
            <th style={{ padding: "8px" }}>Utilisateur</th>
            <th style={{ padding: "8px" }}>Livre</th>
            <th style={{ padding: "8px" }}>Retour prevu</th>
            <th style={{ padding: "8px" }}>Risque</th>
          </tr>
        </thead>
        <tbody>
          {data.risques_retard.map((item) => {
            const couleur =
              item.risque_retard >= 60 ? "#d9534f" :
              item.risque_retard >= 35 ? "#f0ad4e" :
              "#5cb85c";
            return (
              <tr key={item.rental_id} style={{ borderBottom: "1px solid #eee" }}>
                <td style={{ padding: "8px" }}>{item.user}</td>
                <td style={{ padding: "8px" }}>{item.livre}</td>
                <td style={{ padding: "8px" }}>{item.date_retour_prevue}</td>
                <td style={{ padding: "8px", color: couleur, fontWeight: "bold" }}>
                  {item.risque_retard}%
                </td>
              </tr>
            );
          })}
        </tbody>
      </table>
    </div>
  );
}

export default Reporting;
