import { useState } from "react";
import { useNavigate } from "react-router-dom";
import api from "../api";

function Login() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [erreur, setErreur] = useState("");
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    setErreur("");

    try {
      const response = await api.post("/login", { email, password });
      localStorage.setItem("token", response.data.token);
      localStorage.setItem("user", JSON.stringify(response.data.user));
      navigate("/livres");
    } catch (err) {
      setErreur("Identifiants invalides.");
    }
  };

  return (
    <div style={{ maxWidth: "400px", margin: "80px auto", fontFamily: "sans-serif" }}>
      <h1>Connexion</h1>
      <form onSubmit={handleSubmit}>
        <div style={{ marginBottom: "12px" }}>
          <label>Email</label>
          <br />
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
            style={{ width: "100%", padding: "8px" }}
          />
        </div>
        <div style={{ marginBottom: "12px" }}>
          <label>Mot de passe</label>
          <br />
          <input
            type="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
            style={{ width: "100%", padding: "8px" }}
          />
        </div>
        {erreur && <p style={{ color: "red" }}>{erreur}</p>}
        <button type="submit" style={{ padding: "8px 16px" }}>
          Se connecter
        </button>
      </form>
    </div>
  );
}

export default Login;