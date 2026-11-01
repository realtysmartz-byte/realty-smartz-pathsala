/**
 * Submits the candidate's onboarding data to Realty Smartz HRMS.
 * @param {Object} candidateData
 * @returns {Promise<Object>} Created HRMS Employee
 */
export async function syncCandidateToHRMS(candidateData) {
  const HRMS_API_URL = "https://rshrms.in/api/integrations/onboard-candidate";
  const HRMS_SECRET_KEY = "rs_onboarding_secret_2026";

  const payload = {
    firstName: candidateData.firstName?.trim(),
    lastName: candidateData.lastName?.trim(),
    email: candidateData.email?.trim()?.toLowerCase(),
    phone: candidateData.phone?.trim() || null,
    department: candidateData.department?.trim() || null,
    designation: candidateData.designation?.trim() || null,
    dateOfJoining: candidateData.dateOfJoining || new Date().toISOString().split("T")[0],
    dateOfBirth: candidateData.dateOfBirth || null,
    gender: candidateData.gender || null,
    bloodGroup: candidateData.bloodGroup || null,
    address: candidateData.address || null,
    emergencyContactName: candidateData.emergencyContactName || null,
    emergencyContactPhone: candidateData.emergencyContactPhone || null,
    password: candidateData.password || null,
    profilePhotoUrl: candidateData.profilePhotoUrl || null,
  };

  const response = await fetch(HRMS_API_URL, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "x-hrms-api-key": HRMS_SECRET_KEY,
    },
    body: JSON.stringify(payload),
  });

  const data = await response.json();

  if (!response.ok || !data.success) {
    throw new Error(data.error || "Failed to submit candidate to HRMS portal.");
  }

  return data.employee;
}
