import { Link, useNavigate } from "react-router-dom";
import api from "../api";

function Navigation() {
  const navigate = useNavigate();

  const deconnexion = () => {
    api.post("/logout")
      .catch(() => {})
      .finally(() => {
        localStorage.removeItem("token");
        navigate("/login");
      });
  };

  const lienStyle = {
    color: "#e5e5e5",
    textDecoration: "none",
    marginRight: "20px",
    fontWeight: "500",
  };

  return (
    <nav style={{
      display: "flex",
      alignItems: "center",
      justifyContent: "space-between",
      padding: "16px 24px",
      borderBottom: "1px solid #444",
      fontFamily: "sans-serif",
      backgroundColor: "#1a1a1a",
    }}>
      <div style={{ display: "flex", alignItems: "center" }}>
        <Link to="/livres" style={lienStyle}>Livres</Link>
        <Link to="/mes-locations" style={lienStyle}>Mes locations</Link>
        <Link to="/reporting" style={lienStyle}>Reporting</Link>
        <Link to="/ma-prediction" style={lienStyle}>Ma prediction</Link>
      </div>
      <button
        onClick={deconnexion}
        style={{
          padding: "6px 14px",
          borderRadius: "4px",
          border: "1px solid #666",
          backgroundColor: "#2563eb",
          color: "#fff",
          cursor: "pointer",
        }}
      >
        Deconnexion
      </button>
    </nav>
  );
}

export default Navigation;
