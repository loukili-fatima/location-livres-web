import { useEffect, useState } from "react";
import api from "../api";

// Règle métier identique au backend : 0.5€ par jour de retard
const PENALITE_PAR_JOUR = 0.5;

function calculerJoursRetard(dateRetourPrevue, dateRetourReelle) {
  const prevue = new Date(dateRetourPrevue);
  const reference = dateRetourReelle ? new Date(dateRetourReelle) : new Date();
  const diffMs = reference - prevue;
  const diffJours = Math.floor(diffMs / (1000 * 60 * 60 * 24));
  return diffJours > 0 ? diffJours : 0;
}

function formatDate(dateStr) {
  if (!dateStr) return "—";
  return new Date(dateStr).toLocaleDateString("fr-FR");
}

export default function MesLocations() {
  const [locations, setLocations] = useState([]);
  const [loading, setLoading] = useState(true);
  const [erreur, setErreur] = useState(null);
  const [actionEnCours, setActionEnCours] = useState(null); // id de la location en cours de retour

  const chargerLocations = async () => {
    setLoading(true);
    setErreur(null);
    try {
      const { data } = await api.get("/rentals");
      setLocations(data);
    } catch (err) {
      console.error(err);
      setErreur("Impossible de charger vos locations. Réessayez plus tard.");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    chargerLocations();
  }, []);

  const retournerLivre = async (rentalId) => {
    setActionEnCours(rentalId);
    try {
      await api.post(`/rentals/${rentalId}/return`);
      await chargerLocations();
    } catch (err) {
      console.error(err);
      alert(
        err.response?.data?.message ||
          "Erreur lors du retour du livre. Réessayez."
      );
    } finally {
      setActionEnCours(null);
    }
  };

  if (loading) {
    return (
      <div className="flex justify-center items-center py-20">
        <p className="text-gray-500">Chargement de vos locations...</p>
      </div>
    );
  }

  if (erreur) {
    return (
      <div className="max-w-3xl mx-auto mt-8 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
        {erreur}
      </div>
    );
  }

  const locationsActives = locations.filter((l) => !l.date_retour_reelle);
  const locationsTerminees = locations.filter((l) => l.date_retour_reelle);

  return (
    <div className="max-w-6xl mx-auto px-4 py-8">
      <h1 className="text-2xl font-bold text-gray-900 mb-6">Mes locations</h1>

      {locations.length === 0 && (
        <p className="text-gray-500">
          Vous n'avez encore loué aucun livre.
        </p>
      )}

      {locationsActives.length > 0 && (
        <section className="mb-10">
          <h2 className="text-lg font-semibold text-gray-800 mb-4">
            En cours ({locationsActives.length})
          </h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {locationsActives.map((location) => {
              const joursRetard = calculerJoursRetard(
                location.date_retour_prevue,
                null
              );
              const enRetard = joursRetard > 0;
              const penalite = (joursRetard * PENALITE_PAR_JOUR).toFixed(2);

              return (
                <div
                  key={location.id}
                  className={`rounded-xl border p-4 shadow-sm bg-white flex flex-col gap-2 ${
                    enRetard ? "border-red-300" : "border-gray-200"
                  }`}
                >
                  <div className="flex justify-between items-start gap-2">
                    <h3 className="font-semibold text-gray-900 leading-snug">
                      {location.book?.titre || "Livre inconnu"}
                    </h3>
                    {enRetard && (
                      <span className="shrink-0 inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-semibold px-2 py-1 rounded-full">
                        ⚠ En retard
                      </span>
                    )}
                  </div>

                  {location.book?.author && (
                    <p className="text-sm text-gray-500">
                      {location.book.author.nom}
                    </p>
                  )}

                  <div className="text-sm text-gray-600 mt-2 space-y-1">
                    <p>Emprunté le : {formatDate(location.date_emprunt)}</p>
                    <p>
                      Retour prévu le :{" "}
                      {formatDate(location.date_retour_prevue)}
                    </p>
                  </div>

                  {enRetard && (
                    <p className="text-sm font-medium text-red-600">
                      {joursRetard} jour{joursRetard > 1 ? "s" : ""} de retard
                      — pénalité estimée : {penalite} €
                    </p>
                  )}

                  <button
                    onClick={() => retournerLivre(location.id)}
                    disabled={actionEnCours === location.id}
                    className="mt-3 w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 text-white font-medium py-2 rounded-lg transition-colors"
                  >
                    {actionEnCours === location.id
                      ? "Retour en cours..."
                      : "Retourner ce livre"}
                  </button>
                </div>
              );
            })}
          </div>
        </section>
      )}

      {locationsTerminees.length > 0 && (
        <section>
          <h2 className="text-lg font-semibold text-gray-800 mb-4">
            Historique ({locationsTerminees.length})
          </h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {locationsTerminees.map((location) => {
              const joursRetard = calculerJoursRetard(
                location.date_retour_prevue,
                location.date_retour_reelle
              );
              const penalite = (joursRetard * PENALITE_PAR_JOUR).toFixed(2);

              return (
                <div
                  key={location.id}
                  className="rounded-xl border border-gray-200 p-4 shadow-sm bg-gray-50 flex flex-col gap-2 opacity-90"
                >
                  <h3 className="font-semibold text-gray-700 leading-snug">
                    {location.book?.titre || "Livre inconnu"}
                  </h3>
                  {location.book?.author && (
                    <p className="text-sm text-gray-500">
                      {location.book.author.nom}
                    </p>
                  )}
                  <div className="text-sm text-gray-600 mt-2 space-y-1">
                    <p>Emprunté le : {formatDate(location.date_emprunt)}</p>
                    <p>
                      Retourné le : {formatDate(location.date_retour_reelle)}
                    </p>
                  </div>
                  {joursRetard > 0 && (
                    <p className="text-sm font-medium text-red-500">
                      Rendu avec {joursRetard} jour{joursRetard > 1 ? "s" : ""}{" "}
                      de retard — pénalité : {penalite} €
                    </p>
                  )}
                </div>
              );
            })}
          </div>
        </section>
      )}
    </div>
  );
}
