import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
import Login from "./pages/Login";
import Livres from "./pages/Livres";
import MesLocations from "./pages/MesLocations";
import Reporting from "./pages/Reporting";
import MaPrediction from "./pages/MaPrediction";
import CategorieFavorite from "./pages/CategorieFavorite";
import Navigation from "./components/Navigation";
import RouteProtegee from "./components/RouteProtegee";
import { useLocation } from "react-router-dom";

function AppContent() {
  const location = useLocation();
  const cacherNav = location.pathname === "/login" || location.pathname === "/";
  return (
    <>
      {!cacherNav && <Navigation />}
      <Routes>
        <Route path="/" element={<Navigate to="/login" />} />
        <Route path="/login" element={<Login />} />
        <Route path="/livres" element={<RouteProtegee><Livres /></RouteProtegee>} />
        <Route path="/mes-locations" element={<RouteProtegee><MesLocations /></RouteProtegee>} />
        <Route path="/reporting" element={<RouteProtegee><Reporting /></RouteProtegee>} />
          <Route path="/ma-prediction" element={<RouteProtegee><MaPrediction /></RouteProtegee>} />
          <Route path="/categorie-favorite" element={<RouteProtegee><CategorieFavorite /></RouteProtegee>} />
      </Routes>
        </>
  );
}

function App() {
  return (
    <BrowserRouter>
      <AppContent />
    </BrowserRouter>
  );
}

export default App;